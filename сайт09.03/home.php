<?php
session_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная страница</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
            color: #333;
            overflow-x: hidden; /* Prevent horizontal scrolling */
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
            padding: 40px 20px;
            text-align: center;
            max-width: 800px;
            margin: 0 auto;
            opacity: 0;
            transform: translateY(50px);
            animation: fadeIn 1s ease-in-out forwards;
        }
        h1 {
            font-size: 2.5em;
            margin-bottom: 20px;
            color: #007bff;
        }
        p {
            font-size: 1.2em;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .auth-message {
            background-color: #e7f3fe;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            color: #004085;
            opacity: 0;
            transform: translateY(50px);
            animation: fadeIn 1s ease-in-out forwards;
        }
        .auth-message a {
            color: #004085;
            text-decoration: underline;
        }
        .auth-message a:hover {
            color: #005bb5;
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
            <a href="users.php">Пользователи</a>
            <a href="user_profile.php?id=<?php echo $_SESSION['user_id']; ?>">Профиль</a>
            <?php if ($_SESSION['status'] === 'admin'): ?>
                <a href="admin.php">Админка</a>
            <?php endif; ?>
            <a href="logout.php">Выйти</a>
            <span>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['login']); ?> (<?php echo htmlspecialchars($_SESSION['status']); ?>)</span>
        <?php else: ?>
            <a href="login.php">Войти</a>
            <a href="register.php">Зарегистрироваться</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <h1>Главная страница</h1>
    <p>Этот контент доступен всем пользователям.</p>

    <?php if (isset($_SESSION['auth']) && $_SESSION['auth'] === true): ?>
        <div class="auth-message">
            <p>Этот контент доступен только авторизованным пользователям.</p>
            <p>Добро пожаловать, <?php echo htmlspecialchars($_SESSION['login']); ?>!</p>
        </div>
    <?php else: ?>
        <div class="auth-message">
            <p>Пожалуйста, <a href="login.php">авторизуйтесь</a> или <a href="register.php">зарегистрируйтесь</a>, чтобы увидеть этот контент.</p>
        </div>
    <?php endif; ?>
</div>
<div class="footer">
    <p>&copy; 2023 NORDY. Все права защищены.</p>
</div>
</body>
</html>
