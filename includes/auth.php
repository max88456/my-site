<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function isLoggedIn(): bool { return isset($_SESSION['user_id']); }
function currentUserId(): ?int { return $_SESSION['user_id'] ?? null; }
function currentUserRole(): ?string { return $_SESSION['role'] ?? null; }
function currentUserName(): string { return $_SESSION['full_name'] ?? 'Пользователь'; }
function currentUserEmail(): string { return $_SESSION['email'] ?? ''; }
function e($value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }

function requireLogin(): void { if (!isLoggedIn()) { header('Location: /login.php'); exit; } }
function requireRole(array $roles): void { requireLogin(); if (!in_array(currentUserRole(), $roles, true)) { header('Location: /dashboard.php'); exit; } }
function redirectByRole(): void { header('Location: ' . (isLoggedIn() ? '/dashboard.php' : '/login.php')); exit; }

function csrfToken(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function checkCsrf(): void { if ($_SERVER['REQUEST_METHOD'] === 'POST' && (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf']))) { die('Ошибка безопасности CSRF. Обновите страницу.'); } }
function flash(string $type, string $message): void { $_SESSION['flash'][] = ['type'=>$type,'message'=>$message]; }
function getFlashes(): array { $f = $_SESSION['flash'] ?? []; unset($_SESSION['flash']); return $f; }

function actionLabel(string $action): string {
    $map = [
        'login' => 'Вход в систему',
        'logout' => 'Выход из системы',
        'finish_test' => 'Завершение теста',
        'start_test' => 'Начало теста',
        'create_test' => 'Создание теста',
        'update_test' => 'Редактирование теста',
        'delete_test' => 'Удаление теста',
        'create_user' => 'Создание пользователя',
        'update_user' => 'Редактирование пользователя',
        'delete_user' => 'Удаление пользователя',
        'create_subject' => 'Создание предмета',
        'update_subject' => 'Редактирование предмета',
        'delete_subject' => 'Удаление предмета',
        'create_group' => 'Создание группы',
        'update_group' => 'Редактирование группы',
        'delete_group' => 'Удаление группы',
        'import_questions' => 'Импорт вопросов',
        'ai_import_questions' => 'Импорт вопросов через ИИ',
        'generate_ai_questions' => 'Генерация вопросов через ИИ',
        'toggle_user' => 'Изменение статуса пользователя',
        'export_results' => 'Экспорт результатов',
        'Обновление профиля' => 'Обновление профиля'
    ];
    return $map[$action] ?? $action;
}

function logAction(PDO $pdo, ?int $userId, string $action, string $description = ''): void {
    try { $stmt = $pdo->prepare('INSERT INTO activity_log (user_id, action, description, ip_address) VALUES (?, ?, ?, ?)'); $stmt->execute([$userId, $action, $description, $_SERVER['REMOTE_ADDR'] ?? null]); } catch (Throwable $e) {}
}
function notifyUser(PDO $pdo, int $userId, string $title, string $message): void {
    try { $stmt = $pdo->prepare('INSERT INTO notifications (user_id, title, message) VALUES (?, ?, ?)'); $stmt->execute([$userId, $title, $message]); } catch (Throwable $e) {}
}
function roleLabel(?string $role): string { return ['admin'=>'Администратор','teacher'=>'Преподаватель','student'=>'Студент'][$role] ?? 'Гость'; }
function statusBadge(string $status): string { $map = ['draft'=>'Черновик','published'=>'Опубликован','closed'=>'Закрыт','in_progress'=>'В процессе','finished'=>'Завершён','expired'=>'Истёк']; return $map[$status] ?? $status; }
function percentClass(int $score): string { if ($score >= 80) return 'success'; if ($score >= 60) return 'warning'; return 'danger'; }
?>
