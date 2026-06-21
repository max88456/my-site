<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/../config/database.php';
$role = currentUserRole();
$pageTitle = $pageTitle ?? 'StudTest';
$active = $_SERVER['REQUEST_URI'] ?? '';
$unreadCount = 0;
if (isLoggedIn()) {
    try { $s = $pdo->prepare('SELECT COUNT(*) c FROM notifications WHERE user_id=? AND is_read=0'); $s->execute([currentUserId()]); $unreadCount = (int)$s->fetch()['c']; } catch (Throwable $e) {}
}
function navActive($path, $active) { return strpos($active, $path) !== false ? 'active' : ''; }
?>
<!DOCTYPE html>
<html lang="ru" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — StudTest</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<div class="noise"></div>
<div class="app-shell">
    <aside class="sidebar glass" id="sidebar">
        <a class="brand" href="/dashboard.php">
            <div class="brand-logo pulse">ST</div>
            <div><strong>StudTest</strong><span>Smart Testing Platform</span></div>
        </a>
        <nav class="menu">
            <a class="<?= navActive('/dashboard.php',$active) ?>" href="/dashboard.php"><span>🏠</span> Главная</a>
            <?php if ($role === 'admin'): ?>
                <a class="<?= navActive('/admin/users.php',$active) ?>" href="/admin/users.php"><span>👥</span> Пользователи</a>
                <a class="<?= navActive('/admin/groups.php',$active) ?>" href="/admin/groups.php"><span>🎓</span> Группы</a>
                <a class="<?= navActive('/admin/subjects.php',$active) ?>" href="/admin/subjects.php"><span>📚</span> Предметы</a>
                <a class="<?= navActive('/admin/tests.php',$active) ?>" href="/admin/tests.php"><span>🧪</span> Все тесты</a>
                <a class="<?= navActive('/admin/analytics.php',$active) ?>" href="/admin/analytics.php"><span>📈</span> Аналитика</a>
            <?php elseif ($role === 'teacher'): ?>
                <a class="<?= navActive('/teacher/tests.php',$active) ?>" href="/teacher/tests.php"><span>🧪</span> Мои тесты</a>
                <a class="<?= navActive('/teacher/test-create.php',$active) ?>" href="/teacher/test-create.php"><span>✨</span> Создать тест</a>
                <a class="<?= navActive('/teacher/question-bank.php',$active) ?>" href="/teacher/question-bank.php"><span>🏦</span> Банк вопросов</a>
                <a class="<?= navActive('/teacher/import-questions.php',$active) ?>" href="/teacher/import-questions.php"><span>📥</span> Импорт вопросов</a>
                <a class="<?= navActive('/teacher/results.php',$active) ?>" href="/teacher/results.php"><span>📊</span> Результаты</a>
                <a class="<?= navActive('/teacher/analytics.php',$active) ?>" href="/teacher/analytics.php"><span>📈</span> Аналитика</a>
            <?php elseif ($role === 'student'): ?>
                <a class="<?= navActive('/student/tests.php',$active) ?>" href="/student/tests.php"><span>🚀</span> Доступные тесты</a>
                <a class="<?= navActive('/student/results.php',$active) ?>" href="/student/results.php"><span>🏆</span> Мои результаты</a>
                <a class="<?= navActive('/student/calendar.php',$active) ?>" href="/student/calendar.php"><span>📅</span> Календарь</a>
                <a class="<?= navActive('/student/certificates.php',$active) ?>" href="/student/certificates.php"><span>🎖️</span> Сертификаты</a>
            <?php endif; ?>
            <a class="<?= navActive('/logs.php',$active) ?>" href="/logs.php"><span>🕘</span> Журнал</a>
            <a class="<?= navActive('/notifications.php',$active) ?>" href="/notifications.php"><span>🔔</span> Уведомления <?= $unreadCount ? '<b class="nav-dot">'.$unreadCount.'</b>' : '' ?></a>
            <a class="<?= navActive('/help.php',$active) ?>" href="/help.php"><span>❓</span> Справка</a>
            <a class="<?= navActive('/profile.php',$active) ?>" href="/profile.php"><span>👤</span> Профиль</a>
        </nav>
        <div class="sidebar-card">
            <div class="mini-title">Статус</div>
            <strong><?= e(roleLabel($role)) ?></strong>
            <small><?= e(currentUserEmail()) ?></small>
        </div>
    </aside>
    <main class="main">
        <header class="topbar glass">
            <button class="icon-btn" onclick="toggleSidebar()">☰</button>
            <div class="top-title"><h1><?= e($pageTitle) ?></h1><p>Онлайн-тестирование нового поколения</p></div>
            <div class="top-actions">
                <button class="icon-btn" onclick="toggleTheme()" title="Тема">🌙</button>
                <a class="notify-btn" href="/notifications.php">🔔<?= $unreadCount ? '<span>'.$unreadCount.'</span>' : '' ?></a>
                <div class="userchip"><div class="avatar"><?= e(function_exists('mb_substr') ? mb_substr(currentUserName(),0,1,'UTF-8') : substr(currentUserName(),0,1)) ?></div><div><b><?= e(currentUserName()) ?></b><small><?= e(roleLabel($role)) ?></small></div></div>
                <a class="btn btn-light" href="/logout.php">Выйти</a>
            </div>
        </header>
        <section class="content fade-in">
            <?php foreach (getFlashes() as $f): ?><div class="alert <?= e($f['type']) ?>"><?= e($f['message']) ?></div><?php endforeach; ?>
