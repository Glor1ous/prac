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

    $stmt = $pdo->prepare("SELECT name, email, birthdate, country FROM users WHERE id = :id");
    $stmt->execute(['id' => $userId]);
    $user = $stmt->fetch();

    if ($user) {
       
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = htmlspecialchars($_POST['name']);
            $email = htmlspecialchars($_POST['email']);
            $birthdate = $_POST['birthdate'];
            $country = htmlspecialchars($_POST['country']);

           
            $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, birthdate = :birthdate, country = :country WHERE id = :id");
            $stmt->execute([
                'name' => $name,
                'email' => $email,
                'birthdate' => $birthdate,
                'country' => $country,
                'id' => $userId
            ]);

            header('Location: user_profile.php?id=' . $userId);
            exit;
        }
    } else {
        $error = "Пользователь не найден.";
    }
} else {
    $error = "Неверный ID пользователя.";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование профиля</title>
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
        input[type="text"],
        input[type="email"],
        input[type="date"],
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
        <h1>Редактирование профиля</h1>
        <?php if (isset($error)): ?>
            <p><?php echo $error; ?></p>
        <?php elseif ($user): ?>
            <form method="post" action="">
                <label for="name">Имя:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label for="birthdate">Дата рождения (дд.мм.гггг):</label>
                <input type="text" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($user['birthdate']); ?>" required>

                <label for="country">Страна проживания:</label>
                <select id="country" name="country" required>
                    <option value="">Выберите страну</option>
                    <option value="Russia" <?php if ($user['country'] == 'Russia') echo 'selected'; ?>>Россия</option>
                    <option value="USA" <?php if ($user['country'] == 'USA') echo 'selected'; ?>>США</option>
                    <option value="Germany" <?php if ($user['country'] == 'Germany') echo 'selected'; ?>>Германия</option>
                    <!-- Добавьте другие страны по необходимости -->
                </select>

                <input type="submit" value="Сохранить">
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
