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

$userId = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($userId) {
    $stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();

    if ($user) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $currentPassword = $_POST['current_password'];
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];

            $stmt = $pdo->prepare("SELECT password, salt FROM users WHERE id = :id");
            $stmt->execute(['id' => $userId]);
            $currentUserPassword = $stmt->fetch();

            if (password_verify($currentUserPassword['salt'] . $currentPassword, $currentUserPassword['password'])) {
                if ($newPassword === $confirmPassword) {
                    $salt = generateSalt();
                    $hashedPassword = password_hash($salt . $newPassword, PASSWORD_DEFAULT);

                    $stmt = $pdo->prepare("UPDATE users SET password = :password, salt = :salt WHERE id = :id");
                    $stmt->execute(['password' => $hashedPassword, 'salt' => $salt, 'id' => $userId]);

                    $success = "Пароль успешно изменен.";
                } else {
                    $error = "Новые пароли не совпадают.";
                }
            } else {
                $error = "Текущий пароль неверен.";
            }
        }
    } else {
        $error = "Пользователь не найден.";
    }
} else {
    $error = "Неверный ID пользователя.";
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
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Смена пароля</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #007bff;
            color: #fff;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        nav a {
            color: #fff;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }
        nav a:hover {
            color: #ddd;
        }
        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            margin: 20px auto;
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
        input[type="password"] {
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
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="home.php">Главная</a>
            <?php if (isset($_SESSION['auth']) && $_SESSION['auth'] === true): ?>
                <a href="1.php">Страница 1</a>
                <a href="2.php">Страница 2</a>
                <a href="3.php">Страница 3</a>
                <a href="user_profile.php?id=<?php echo $_SESSION['user_id']; ?>">Профиль</a>
                <a href="users.php">Пользователи</a>
                <a href="logout.php">Выйти</a>
            <?php else: ?>
                <a href="login.php">Войти</a>
                <a href="register.php">Зарегистрироваться</a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="container">
        <h1>Смена пароля</h1>
        <?php if (isset($error)): ?>
            <p><?php echo $error; ?></p>
        <?php elseif (isset($success)): ?>
            <p><?php echo $success; ?></p>
        <?php elseif ($user): ?>
            <form method="post" action="">
                <label for="current_password">Текущий пароль:</label>
                <input type="password" id="current_password" name="current_password" required>

                <label for="new_password">Новый пароль:</label>
                <input type="password" id="new_password" name="new_password" required>

                <label for="confirm_password">Подтвердите новый пароль:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <input type="submit" value="Сменить пароль">
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
