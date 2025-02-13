<?php
session_start();
$_SESSION['auth'] = null;
unset($_SESSION['login']);
header('Location: home.php?logout=true');
exit;
?>
