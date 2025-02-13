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

$stmt = $pdo->prepare("SELECT id, name FROM users");
$stmt->execute();
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Список пользователей</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            color: #333;
            overflow-x: hidden; 
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
            padding: 40px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: 40px auto;
            text-align: center;
            opacity: 0;
            transform: translateY(50px);
            animation: fadeIn 1s ease-in-out forwards;
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #007bff;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 15px 0;
            background-color: #e7f3fe;
            padding: 10px;
            border-radius: 8px;
            transition: background-color 0.3s;
            opacity: 0;
            transform: translateY(50px);
            animation: fadeIn 1s ease-in-out forwards;
        }
        li:hover {
            background-color: #cce0ff;
        }
        a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
        .footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
            opacity: 0;
            transform: translateY(50px);
            animation: fadeIn 1s ease-in-out forwards;
        }
        .footer p {
            margin: 0;
        }
        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
    <h1>Список пользователей</h1>
    <ul>
        <?php foreach ($users as $user): ?>
            <li><a href="profile.php?id=<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['name']); ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>
<div class="footer">
    <p>&copy; 2023 NORDY. Все права защищены.</p>
</div>
</body>
</html>
