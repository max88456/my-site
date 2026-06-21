<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) {
    logAction($pdo, currentUserId(), 'logout', 'Выход из системы');
}
session_destroy();
header('Location: /login.php');
exit;
?>
