<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);
$pageTitle = 'Группы';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $pdo->prepare('DELETE FROM student_groups WHERE id=?')->execute([$id]);
        logAction($pdo, currentUserId(), 'delete_group', 'Удалена группа #' . $id);
        flash('success', 'Группа удалена.');
    } catch (Throwable $e) {
        flash('danger', 'Группу нельзя удалить, потому что к ней привязаны студенты или тесты. Сначала измените привязки.');
    }
    header('Location: /admin/groups.php'); exit;
}

$editGroup = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM student_groups WHERE id=?');
    $stmt->execute([(int)$_GET['edit']]);
    $editGroup = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $course = (int)($_POST['course'] ?? 1);
    $curator = trim($_POST['curator_name'] ?? '');

    if ($name !== '') {
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE student_groups SET name=?, course=?, curator_name=? WHERE id=?');
            $stmt->execute([$name, $course, $curator, $id]);
            logAction($pdo, currentUserId(), 'update_group', 'Изменена группа #' . $id);
            flash('success', 'Группа обновлена.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO student_groups (name, course, curator_name) VALUES (?, ?, ?)');
            $stmt->execute([$name, $course, $curator]);
            logAction($pdo, currentUserId(), 'create_group', 'Добавлена группа');
            flash('success', 'Группа добавлена.');
        }
    }
    header('Location: /admin/groups.php'); exit;
}
$groups = $pdo->query('SELECT * FROM student_groups ORDER BY course, name')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="grid-2">
    <div class="card">
        <div class="table-toolbar">
            <div><h2><?= $editGroup ? 'Изменить группу' : 'Добавить группу' ?></h2><p class="muted-text">Настройка учебных групп, курса и куратора.</p></div>
            <?php if ($editGroup): ?><a class="btn btn-light" href="/admin/groups.php">Отмена</a><?php endif; ?>
        </div>
        <form method="post" class="form">
            <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="id" value="<?= $editGroup ? (int)$editGroup['id'] : 0 ?>">
            <label>Название группы <input name="name" value="<?= e($editGroup['name'] ?? '') ?>" required></label>
            <label>Курс <input type="number" name="course" min="1" max="4" value="<?= e($editGroup['course'] ?? '') ?>" required></label>
            <label>Куратор <input name="curator_name" value="<?= e($editGroup['curator_name'] ?? '') ?>"></label>
            <button class="btn"><?= $editGroup ? 'Сохранить изменения' : 'Сохранить' ?></button>
        </form>
    </div>
    <div class="card table-wrap">
        <table><tr><th>Группа</th><th>Курс</th><th>Куратор</th><th>Действия</th></tr>
        <?php foreach ($groups as $g): ?>
            <tr>
                <td><?= e($g['name']) ?></td>
                <td><?= (int)$g['course'] ?></td>
                <td><?= e($g['curator_name']) ?></td>
                <td><div class="action-row"><a class="btn btn-sm btn-light" href="?edit=<?= (int)$g['id'] ?>">Изменить</a><a class="btn btn-sm btn-danger" href="?delete=<?= (int)$g['id'] ?>" data-confirm="Удалить группу?">Удалить</a></div></td>
            </tr>
        <?php endforeach; ?>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
