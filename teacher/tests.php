<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['teacher']);
$pageTitle = 'Мои тесты';
$stmt = $pdo->prepare('SELECT t.*, s.name AS subject_name, COUNT(q.id) AS questions_count FROM tests t JOIN subjects s ON s.id=t.subject_id LEFT JOIN questions q ON q.test_id=t.id WHERE t.teacher_id=? GROUP BY t.id ORDER BY t.id DESC');
$stmt->execute([currentUserId()]);
$tests = $stmt->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="actions" style="margin-bottom:16px;display:flex;gap:10px;flex-wrap:wrap"><a class="btn" href="/teacher/test-create.php">Создать тест</a><a class="btn btn-light" href="/teacher/import-questions.php">Импорт вопросов</a></div>
<div class="card table-wrap">
<table><tr><th>Тест</th><th>Предмет</th><th>Вопросов</th><th>Статус</th><th>Действия</th></tr>
<?php foreach ($tests as $t): ?><tr><td><?= htmlspecialchars($t['title']) ?></td><td><?= htmlspecialchars($t['subject_name']) ?></td><td><?= $t['questions_count'] ?></td><td><span class="badge"><?= $t['status'] ?></span></td><td><a class="btn btn-light" href="/teacher/questions.php?test_id=<?= $t['id'] ?>">Вопросы</a> <a class="btn btn-ghost" href="/teacher/import-questions.php?test_id=<?= $t['id'] ?>">Импорт</a></td></tr><?php endforeach; ?>
</table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
