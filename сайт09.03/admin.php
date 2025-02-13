<?php
session_start();

if (!isset($_SESSION['auth']) || $_SESSION['status'] !== 'admin') {
    header('Location: home.php');
    exit;
}

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

$stmt = $pdo->query("SELECT id, name, status FROM users");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админка</title>
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
            width: 800px;
            margin: 20px auto;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #f0f0f0;
        }
        .admin-row {
            background-color: #ffe6e6;
        }
        .user-row {
            background-color: #e6ffe6;
        }
        .delete-link a {
            color: #ff0000;
            text-decoration: none;
        }
        .delete-link a:hover {
            text-decoration: underline;
        }
        .change-status-link a {
            color: #007bff;
            text-decoration: none;
        }
        .change-status-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <a href="home.php">Главная</a>
            <a href="admin.php">Админка</a>
            <a href="logout.php">Выйти</a>
        </nav>
    </header>
    <div class="container">
        <h1>Админка</h1>
        <table>
            <tr>
            <th>id</th>
                <th>Имя</th>
                <th>Статус</th>
                <th>Удалить</th>
                <th>Изменить статус</th>
            </tr>
            <?php foreach ($users as $user): ?>
                <tr class="<?php echo $user['status'] === 'admin' ? 'admin-row' : 'user-row'; ?>">
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['status']); ?></td>
                    <td class="delete-link">
                        <a href="delete_user.php?id=<?php echo $user['id']; ?>">Удалить</a>
                    </td>
                    <td class="change-status-link">
                        <?php if ($user['status'] === 'admin'): ?>
                            <a href="change_status.php?id=<?php echo $user['id']; ?>&status=user">Сделать юзером</a>
                        <?php else: ?>
                            <a href="change_status.php?id=<?php echo $user['id']; ?>&status=admin">Сделать админом</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
