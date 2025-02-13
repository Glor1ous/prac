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
      
        $birthdate = DateTime::createFromFormat('Y-m-d', $user['birthdate']);
        $now = new DateTime();
        $age = $now->diff($birthdate)->y;
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
    <title>Профиль пользователя</title>
    <style>
         body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            color: #333;
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
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 40px auto;
            text-align: center;
        }
        h1 {
            font-size: 2em;
            margin-bottom: 20px;
            color: #007bff;
        }
        p {
            font-size: 1.2em;
            margin: 15px 0;
        }
        .profile-info {
            background-color: #e7f3fe;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .profile-info p {
            margin: 10px 0;
        }
        .edit-link, .delete-link {
            margin-top: 20px;
        }
        .edit-link a, .delete-link a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            transition: color 0.3s;
        }
        .edit-link a:hover, .delete-link a:hover {
            color: #005bb5;
        }
        .delete-link a {
            color: #ff0000;
        }
        .delete-link a:hover {
            color: #cc0000;
        }
        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
        .footer p {
            margin: 0;
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
                <a href="users.php">Пользователи</a>
                <a href="user_profile.php?id=<?php echo $_SESSION['user_id']; ?>">Профиль</a>
                <a href="logout.php">Выйти</a>
            <?php else: ?>
                <a href="login.php">Войти</a>
                <a href="register.php">Зарегистрироваться</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="container">
        <h1>Профиль пользователя</h1>
        <?php if (isset($error)): ?>
            <p><?php echo $error; ?></p>
        <?php elseif ($user): ?>
            <p><strong>Имя:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Возраст:</strong> <?php echo $age; ?> лет</p>
            <p><strong>Страна проживания:</strong> <?php echo htmlspecialchars($user['country']); ?></p>
        <?php endif; ?>
    </div>
    <div class="footer">
        <p>&copy; 2023 NORDY. Все права защищены.</p>
    </div>
</body>
</html>
