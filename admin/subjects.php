<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);
$pageTitle = 'Предметы';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $pdo->prepare('DELETE FROM subjects WHERE id=?')->execute([$id]);
        logAction($pdo, currentUserId(), 'delete_subject', 'Удалён предмет #' . $id);
        flash('success', 'Предмет удалён.');
    } catch (Throwable $e) {
        flash('danger', 'Предмет нельзя удалить, потому что к нему привязаны тесты.');
    }
    header('Location: /admin/subjects.php'); exit;
}

$editSubject = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare('SELECT * FROM subjects WHERE id=?');
    $stmt->execute([(int)$_GET['edit']]);
    $editSubject = $stmt->fetch();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrf();
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $teacherId = !empty($_POST['teacher_id']) ? (int)$_POST['teacher_id'] : null;

    if ($name !== '') {
        if ($id > 0) {
            $stmt = $pdo->prepare('UPDATE subjects SET name=?, description=?, teacher_id=? WHERE id=?');
            $stmt->execute([$name, $description, $teacherId, $id]);
            logAction($pdo, currentUserId(), 'update_subject', 'Изменён предмет #' . $id);
            flash('success', 'Предмет обновлён.');
        } else {
            $stmt = $pdo->prepare('INSERT INTO subjects (name, description, teacher_id) VALUES (?, ?, ?)');
            $stmt->execute([$name, $description, $teacherId]);
            logAction($pdo, currentUserId(), 'create_subject', 'Добавлен предмет');
            flash('success', 'Предмет добавлен.');
        }
    }
    header('Location: /admin/subjects.php'); exit;
}
$teachers = $pdo->query("SELECT id, full_name FROM users WHERE role='teacher' ORDER BY full_name")->fetchAll();
$subjects = $pdo->query('SELECT s.*, u.full_name AS teacher FROM subjects s LEFT JOIN users u ON u.id=s.teacher_id ORDER BY s.id DESC')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="grid-2">
    <div class="card">
        <div class="table-toolbar">
            <div><h2><?= $editSubject ? 'Изменить предмет' : 'Добавить предмет' ?></h2><p class="muted-text">Можно поменять название, описание и преподавателя.</p></div>
            <?php if ($editSubject): ?><a class="btn btn-light" href="/admin/subjects.php">Отмена</a><?php endif; ?>
        </div>
        <form method="post" class="form">
            <input type="hidden" name="csrf" value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="id" value="<?= $editSubject ? (int)$editSubject['id'] : 0 ?>">
            <label>Название <input name="name" value="<?= e($editSubject['name'] ?? '') ?>" required></label>
            <label>Описание <textarea name="description"><?= e($editSubject['description'] ?? '') ?></textarea></label>
            <label>Преподаватель
                <select name="teacher_id"><option value="">Не выбран</option><?php foreach ($teachers as $t): ?><option value="<?= (int)$t['id'] ?>" <?= isset($editSubject['teacher_id']) && (int)$editSubject['teacher_id']===(int)$t['id']?'selected':'' ?>><?= e($t['full_name']) ?></option><?php endforeach; ?></select>
            </label>
            <button class="btn"><?= $editSubject ? 'Сохранить изменения' : 'Сохранить' ?></button>
        </form>
    </div>
    <div class="card table-wrap">
        <table><tr><th>Предмет</th><th>Преподаватель</th><th>Действия</th></tr>
        <?php foreach ($subjects as $s): ?>
            <tr>
                <td><?= e($s['name']) ?></td>
                <td><?= e($s['teacher'] ?? '-') ?></td>
                <td><div class="action-row"><a class="btn btn-sm btn-light" href="?edit=<?= (int)$s['id'] ?>">Изменить</a><a class="btn btn-sm btn-danger" href="?delete=<?= (int)$s['id'] ?>" data-confirm="Удалить предмет?">Удалить</a></div></td>
            </tr>
        <?php endforeach; ?>
        </table>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
