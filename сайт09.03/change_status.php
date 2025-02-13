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

if (isset($_GET['id']) && isset($_GET['status'])) {
    $userId = intval($_GET['id']);
    $newStatus = $_GET['status'] === 'admin' ? 'admin' : 'user';
    $stmt = $pdo->prepare("UPDATE users SET status = :status WHERE id = :id");
    $stmt->execute(['status' => $newStatus, 'id' => $userId]);
    header('Location: admin.php');
    exit;
}
?>
