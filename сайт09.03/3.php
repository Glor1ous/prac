<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Страница 3</title>
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
            padding: 20px;
            text-align: center;
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
                <a href="logout.php">Выйти</a>
            <?php else: ?>
                <a href="login.php">Войти</a>
                <a href="register.php">Зарегистрироваться</a>
            <?php endif; ?>
        </nav>
    </header>
    <div class="container">
    <h1>Страница 3</h1>
    <p>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['login']); ?>!</p>
            </div>
</body>
</html>