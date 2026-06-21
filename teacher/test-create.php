<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['teacher']);
$pageTitle = 'Создать тест';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare('INSERT INTO tests (title, description, subject_id, teacher_id, time_limit, max_attempts, passing_score, status, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([
        trim($_POST['title']), trim($_POST['description']), (int)$_POST['subject_id'], currentUserId(),
        (int)$_POST['time_limit'], (int)$_POST['max_attempts'], (int)$_POST['passing_score'], $_POST['status'],
        $_POST['start_date'] ?: null, $_POST['end_date'] ?: null
    ]);
    $testId = $pdo->lastInsertId();
    foreach ($_POST['groups'] ?? [] as $groupId) {
        $pdo->prepare('INSERT INTO test_groups (test_id, group_id) VALUES (?, ?)')->execute([$testId, (int)$groupId]);
    }
    logAction($pdo, currentUserId(), 'create_test', 'Создан тест #' . $testId);
    header('Location: /teacher/questions.php?test_id=' . $testId); exit;
}
$subjects = $pdo->prepare('SELECT * FROM subjects WHERE teacher_id=? OR teacher_id IS NULL ORDER BY name');
$subjects->execute([currentUserId()]);
$subjects = $subjects->fetchAll();
$groups = $pdo->query('SELECT * FROM student_groups ORDER BY name')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="card">
<form method="post" class="form">
    <label>Название теста <input name="title" required></label>
    <label>Описание <textarea name="description"></textarea></label>
    <div class="grid-2">
        <label>Предмет <select name="subject_id" required><?php foreach ($subjects as $s): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option><?php endforeach; ?></select></label>
        <label>Статус <select name="status"><option value="draft">Черновик</option><option value="published">Опубликован</option><option value="closed">Закрыт</option></select></label>
        <label>Время, минут <input type="number" name="time_limit" value="30" min="1"></label>
        <label>Количество попыток <input type="number" name="max_attempts" value="1" min="1"></label>
        <label>Проходной балл, % <input type="number" name="passing_score" value="50" min="0" max="100"></label>
        <label>Дата начала <input type="datetime-local" name="start_date"></label>
        <label>Дата окончания <input type="datetime-local" name="end_date"></label>
    </div>
    <label>Группы
        <select name="groups[]" multiple size="4"><?php foreach ($groups as $g): ?><option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option><?php endforeach; ?></select>
    </label>
    <button class="btn">Создать и перейти к вопросам</button>
</form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
