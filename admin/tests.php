<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);
$pageTitle = 'Все тесты';
$tests = $pdo->query('SELECT t.*, s.name AS subject_name, u.full_name AS teacher_name FROM tests t JOIN subjects s ON s.id=t.subject_id JOIN users u ON u.id=t.teacher_id ORDER BY t.id DESC')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="card table-wrap">
<table><tr><th>Название</th><th>Предмет</th><th>Преподаватель</th><th>Статус</th><th>Время</th></tr>
<?php foreach ($tests as $t): ?><tr><td><?= htmlspecialchars($t['title']) ?></td><td><?= htmlspecialchars($t['subject_name']) ?></td><td><?= htmlspecialchars($t['teacher_name']) ?></td><td><span class="badge"><?= $t['status'] ?></span></td><td><?= $t['time_limit'] ?> мин.</td></tr><?php endforeach; ?>
</table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
