<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);
$pageTitle = 'Пользователи';

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    if ($id === (int)currentUserId()) {
        flash('warning', 'Нельзя заблокировать собственную учётную запись.');
    } else {
        $pdo->prepare('UPDATE users SET is_active = IF(is_active=1,0,1) WHERE id=?')->execute([$id]);
        logAction($pdo, currentUserId(), 'toggle_user', 'Изменён статус пользователя #' . $id);
        flash('success', 'Статус пользователя обновлён.');
    }
    header('Location: /admin/users.php'); exit;
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    if ($id === (int)currentUserId()) {
        flash('warning', 'Нельзя удалить собственную учётную запись.');
    } else {
        try {
            $stmt = $pdo->prepare('DELETE FROM users WHERE id=?');
            $stmt->execute([$id]);
            logAction($pdo, currentUserId(), 'delete_user', 'Удалён пользователь #' . $id);
            flash('success', 'Пользователь удалён.');
        } catch (Throwable $e) {
            flash('danger', 'Пользователь связан с тестами или результатами, поэтому удаление недоступно. Можно заблокировать пользователя.');
        }
    }
    header('Location: /admin/users.php'); exit;
}

$editUser = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM users WHERE id=?');
    $stmt->execute([(int)$_GET['edit']]);
    $editUser = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $id = (int)($_POST['id'] ?? 0);
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'student';
    $groupId = !empty($_POST['group_id']) ? (int)$_POST['group_id'] : null;
    $isActive = isset($_POST['is_active']) ? 1 : 0;

    if ($id > 0 && $fullName !== '' && $email !== '') {
        $stmt = $pdo->prepare('UPDATE users SET full_name=?, email=?, role=?, group_id=?, is_active=? WHERE id=?');
        $stmt->execute([$fullName, $email, $role, $groupId, $isActive, $id]);
        logAction($pdo, currentUserId(), 'update_user', 'Обновлён пользователь #' . $id);
        flash('success', 'Данные пользователя сохранены.');
    }
    header('Location: /admin/users.php'); exit;
}

$groups = $pdo->query('SELECT id, name FROM student_groups ORDER BY name')->fetchAll();
$users = $pdo->query('SELECT u.*, g.name AS group_name FROM users u LEFT JOIN student_groups g ON g.id=u.group_id ORDER BY u.id DESC')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<?php if ($editUser): ?>
<div class="card" id="editUserCard">
    <div class="table-toolbar">
        <div><h2>Изменить пользователя</h2><p class="muted-text">Можно поменять роль, группу, email и статус аккаунта.</p></div>
        <a class="btn btn-light" href="/admin/users.php">Отмена</a>
    </div>
    <form method="post" class="form grid grid-2">
        <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
        <input type="hidden" name="id" value="<?= (int)$editUser['id'] ?>">
        <label>ФИО <input name="full_name" value="<?= e($editUser['full_name']) ?>" required></label>
        <label>Email <input type="email" name="email" value="<?= e($editUser['email']) ?>" required></label>
        <label>Роль
            <select name="role">
                <option value="admin" <?= $editUser['role']==='admin'?'selected':'' ?>>Администратор</option>
                <option value="teacher" <?= $editUser['role']==='teacher'?'selected':'' ?>>Преподаватель</option>
                <option value="student" <?= $editUser['role']==='student'?'selected':'' ?>>Студент</option>
            </select>
        </label>
        <label>Группа
            <select name="group_id"><option value="">Не выбрана</option><?php foreach ($groups as $g): ?><option value="<?= (int)$g['id'] ?>" <?= (int)$editUser['group_id']===(int)$g['id']?'selected':'' ?>><?= e($g['name']) ?></option><?php endforeach; ?></select>
        </label>
        <label class="option"><input type="checkbox" name="is_active" value="1" <?= $editUser['is_active'] ? 'checked' : '' ?>> Аккаунт активен</label>
        <div class="form-actions"><button class="btn">Сохранить изменения</button></div>
    </form>
</div>
<?php endif; ?>

<div class="card">
    <div class="table-toolbar">
        <div><h2>Список пользователей</h2><p class="muted-text">Управление доступом студентов, преподавателей и администраторов.</p></div>
    </div>
    <div class="table-wrap">
        <table>
            <tr><th>ФИО</th><th>Email</th><th>Роль</th><th>Группа</th><th>Статус</th><th>Действия</th></tr>
            <?php foreach ($users as $u): ?>
                <tr>
                    <td><?= e($u['full_name']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><span class="badge"><?= e(roleLabel($u['role'])) ?></span></td>
                    <td><?= e($u['group_name'] ?? '-') ?></td>
                    <td><span class="badge <?= $u['is_active'] ? 'success' : 'danger' ?>"><?= $u['is_active'] ? 'Активен' : 'Заблокирован' ?></span></td>
                    <td>
                        <div class="action-row">
                            <a class="btn btn-sm btn-light" href="?edit=<?= (int)$u['id'] ?>">Изменить</a>
                            <a class="btn btn-sm <?= $u['is_active'] ? 'btn-warning' : 'btn-success' ?>" href="?toggle=<?= (int)$u['id'] ?>" data-confirm="<?= $u['is_active'] ? 'Заблокировать пользователя?' : 'Разблокировать пользователя?' ?>"><?= $u['is_active'] ? 'Заблокировать' : 'Разблокировать' ?></a>
                            <a class="btn btn-sm btn-danger" href="?delete=<?= (int)$u['id'] ?>" data-confirm="Удалить пользователя? Это действие нельзя отменить.">Удалить</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
