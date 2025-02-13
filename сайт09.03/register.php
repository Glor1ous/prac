<?php
session_start();

$host = 'localhost';
$db = 'captcha';
$user = 'captcha';
$pass = 'captcha';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

function generateCaptcha() {
    $characters = '0123456789abcdefghjkmnopqrstuvwxyzABCDEFGHJKMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 5; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function generateSalt($length = 22) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['step']) && $_POST['step'] == 1) {
        $name = htmlspecialchars($_POST['name']);
        $last_name = htmlspecialchars($_POST['last_name']);
        $first_name = htmlspecialchars($_POST['first_name']);
        $middle_name = htmlspecialchars($_POST['middle_name']);
        $email = htmlspecialchars($_POST['email']);
        $password = $_POST['password'];
        $birthdate = $_POST['birthdate'];
        $country = $_POST['country'];

        if (!preg_match('/^[a-zA-Z0-9]{4,10}$/', $name)) {
            $error_name = "Логин должен содержать только латинские буквы и цифры и быть длиной от 4 до 10 символов.";
        }

        if (strlen($password) < 6 || strlen($password) > 12) {
            $error_password = "Пароль должен быть длиной от 6 до 12 символов.";
        }

        if (!preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $birthdate)) {
            $error_birthdate = "Дата рождения должна быть в формате день.месяц.год.";
        } else {
            $birthdate = DateTime::createFromFormat('d.m.Y', $birthdate);
            $now = new DateTime();
            $age = $now->diff($birthdate)->y;
            if ($age < 18) {
                $error_age = "Вы должны быть старше 18 лет.";
            }
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $existingUser = $stmt->fetch();

        if ($existingUser) {
            $error = "Пользователь с таким email уже существует. Пожалуйста, используйте другой email.";
        } elseif (!isset($error_name) && !isset($error_password) && !isset($error_birthdate) && !isset($error_age)) {

            $id = uniqid('user_', true);
            $_SESSION['id'] = $id;

            $captchaCode = generateCaptcha();
            $_SESSION['captcha'] = $captchaCode;
            $_SESSION['name'] = $name;
            $_SESSION['last_name'] = $last_name;
            $_SESSION['first_name'] = $first_name;
            $_SESSION['middle_name'] = $middle_name;
            $_SESSION['email'] = $email;
            $_SESSION['password'] = $password;
            $_SESSION['birthdate'] = $birthdate;
            $_SESSION['country'] = $country;

            $stmt = $pdo->prepare("INSERT INTO captcha (captcha_code) VALUES (:captcha_code)");
            $stmt->execute(['captcha_code' => $captchaCode]);

            header('Location: ?step=2');
            exit;
        }
    } elseif (isset($_POST['step']) && $_POST['step'] == 2) {
        $captcha_answer = $_POST['captcha'];

        if ($captcha_answer == $_SESSION['captcha']) {

            $salt = generateSalt();
            $hashedPassword = password_hash($salt . $_SESSION['password'], PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("INSERT INTO users (id, name, last_name, first_name, middle_name, email, password, salt, birthdate, country, status) VALUES (:id, :name, :last_name, :first_name, :middle_name, :email, :password, :salt, :birthdate, :country, 'user')");
            $stmt->execute([
                'id' => $_SESSION['id'],
                'name' => $_SESSION['name'],
                'last_name' => $_SESSION['last_name'],
                'first_name' => $_SESSION['first_name'],
                'middle_name' => $_SESSION['middle_name'],
                'email' => $_SESSION['email'],
                'password' => $hashedPassword,
                'salt' => $salt,
                'birthdate' => $_SESSION['birthdate']->format('Y-m-d'),
                'country' => $_SESSION['country']
            ]);

            $stmt = $pdo->prepare("DELETE FROM captcha WHERE captcha_code = :captcha_code");
            $stmt->execute(['captcha_code' => $_SESSION['captcha']]);

            $_SESSION['auth'] = true;
            $_SESSION['login'] = $_SESSION['name'];
            $_SESSION['status'] = 'user'; // Записываем статус
            $_SESSION['user_id'] = $_SESSION['id']; // Устанавливаем user_id в сессии

            unset($_SESSION['name']);
            unset($_SESSION['last_name']);
            unset($_SESSION['first_name']);
            unset($_SESSION['middle_name']);
            unset($_SESSION['email']);
            unset($_SESSION['password']);
            unset($_SESSION['birthdate']);
            unset($_SESSION['country']);
            unset($_SESSION['captcha']);

            header('Location: home.php');
            exit;
        } else {
            $error = "Неверная CAPTCHA. Пожалуйста, попробуйте снова.";
        }

        unset($_SESSION['name']);
        unset($_SESSION['last_name']);
        unset($_SESSION['first_name']);
        unset($_SESSION['middle_name']);
        unset($_SESSION['email']);
        unset($_SESSION['password']);
        unset($_SESSION['birthdate']);
        unset($_SESSION['country']);
        unset($_SESSION['captcha']);
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"] {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        select {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            padding: 10px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #218838;
        }
        p {
            text-align: center;
            margin-top: 20px;
            color: red;
        }
        .error {
            color: red;
            font-size: 12px;
            margin-top: -10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Регистрация</h1>

        <?php if (isset($error)): ?>
            <p><?php echo $error; ?></p>
        <?php endif; ?>

        <?php if (!isset($_GET['step']) || $_GET['step'] == 1): ?>
            <form method="post" action="">
                <input type="hidden" name="step" value="1">
                <label for="name">Имя:</label>
                <input type="text" id="name" name="name" required>
                <?php if (isset($error_name)): ?>
                    <div class="error"><?php echo $error_name; ?></div>
                <?php endif; ?>

                <label for="last_name">Фамилия:</label>
                <input type="text" id="last_name" name="last_name" required>

                <label for="first_name">Имя:</label>
                <input type="text" id="first_name" name="first_name" required>

                <label for="middle_name">Отчество:</label>
                <input type="text" id="middle_name" name="middle_name" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Пароль:</label>
                <input type="password" id="password" name="password" required>
                <?php if (isset($error_password)): ?>
                    <div class="error"><?php echo $error_password; ?></div>
                <?php endif; ?>

                <label for="birthdate">Дата рождения (дд.мм.гггг):</label>
                <input type="text" id="birthdate" name="birthdate" required>
                <?php if (isset($error_birthdate)): ?>
                    <div class="error"><?php echo $error_birthdate; ?></div>
                <?php endif; ?>
                <?php if (isset($error_age)): ?>
                    <div class="error"><?php echo $error_age; ?></div>
                <?php endif; ?>

                <label for="country">Страна проживания:</label>
                <select id="country" name="country" required>
                    <option value="">Выберите страну</option>
                    <option value="Russia">Россия</option>
                    <option value="USA">США</option>
                    <option value="Germany">Германия</option>
                </select>

                <input type="submit" value="Продолжить">
            </form>
        <?php elseif (isset($_GET['step']) && $_GET['step'] == 2): ?>
            <?php if (isset($_SESSION['captcha'])): ?>
                <form method="post" action="">
                    <input type="hidden" name="step" value="2">
                    <label for="captcha">CAPTCHA: <?php echo $_SESSION['captcha']; ?></label>
                    <input type="text" id="captcha" name="captcha" required>

                    <input type="submit" value="Отправить">
                </form>
            <?php else: ?>
                <p>Ошибка: CAPTCHA не сгенерирована. Пожалуйста, вернитесь назад и попробуйте снова.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
