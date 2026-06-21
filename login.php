<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
if (isLoggedIn()) redirectByRole();
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email=? AND is_active=1 LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($pass, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        logAction($pdo, (int)$user['id'], 'Вход в систему', 'Пользователь авторизовался');
        header('Location: /dashboard.php'); exit;
    }
    $error = 'Неверный email или пароль';
}
?>
<!DOCTYPE html><html lang="ru" data-theme="light"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Вход — StudTest</title><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap" rel="stylesheet"><link rel="stylesheet" href="/assets/css/style.css"><!-- FINAL_INLINE_PASSWORD_FIX --><style>
.password-wrap{position:relative!important;display:block!important;width:100%!important}.password-wrap input{width:100%!important;padding-right:58px!important}.password-icon{position:absolute!important;right:10px!important;top:50%!important;transform:translateY(-50%)!important;width:38px!important;height:38px!important;min-width:38px!important;padding:0!important;margin:0!important;border:0!important;border-radius:13px!important;background:transparent!important;color:#94a3b8!important;box-shadow:none!important;display:flex!important;align-items:center!important;justify-content:center!important;line-height:1!important;cursor:pointer!important;z-index:10!important}.password-icon:hover{background:rgba(37,99,235,.08)!important;color:#2563eb!important}.password-icon svg{width:22px!important;height:22px!important;display:block!important;pointer-events:none!important}.password-icon .icon-eye{fill:none!important;stroke:currentColor!important;stroke-width:2!important;stroke-linecap:round!important;stroke-linejoin:round!important}.password-icon .icon-eye-off{display:none!important}.password-icon.is-visible .icon-eye-open{display:none!important}.password-icon.is-visible .icon-eye-off{display:block!important}
</style></head>
<body class="login-page"><div class="login-orbs"><span></span><span></span><span></span></div>
<form class="login-card glass fade-in" method="post">
    <div class="brand"><div class="brand-logo pulse">ST</div><div><strong>StudTest</strong><span>Smart Testing Platform</span></div></div>
    <h1>Вход в систему</h1><p>Платформа онлайн-тестирования студентов с ролями, статистикой и красивым интерфейсом.</p>
    <?php if ($error): ?><div class="alert danger"><?= e($error) ?></div><?php endif; ?>
    <div class="form">
        <div class="field"><label>Email</label><input name="email" type="email" value="<?= e($_POST['email'] ?? '') ?>" required></div>
        <div class="field"><label>Пароль</label><div class="password-wrap"><input id="loginPassword" name="password" type="password" required><button class="password-icon" type="button" data-toggle-password="loginPassword" aria-label="Показать пароль" title="Показать пароль">
                    <svg class="icon-eye icon-eye-open" viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 12s3.5-6.5 9.5-6.5S21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="icon-eye icon-eye-off" viewBox="0 0 24 24" aria-hidden="true"><path d="M3 3l18 18"/><path d="M10.6 5.7A8.8 8.8 0 0 1 12 5.5c6 0 9.5 6.5 9.5 6.5a16.4 16.4 0 0 1-3.1 3.9"/><path d="M6.5 6.9C3.9 8.7 2.5 12 2.5 12s3.5 6.5 9.5 6.5a9.2 9.2 0 0 0 4.1-.9"/><path d="M9.9 9.9A3 3 0 0 0 14.1 14.1"/></svg>
                </button></div></div>
        <button class="btn" type="submit">Войти</button>
    </div>
    <div class="auth-switch auth-switch-actions auth-switch-stacked"><span>Нет аккаунта?</span><a class="btn btn-light auth-link-btn" href="/register.php">Зарегистрироваться</a></div>
</form><script src="/assets/js/app.js"></script></body></html>
