<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['student']);
$pageTitle = 'Доступные тесты';
$stmt = $pdo->prepare("SELECT t.*, s.name AS subject_name FROM tests t JOIN subjects s ON s.id=t.subject_id JOIN test_groups tg ON tg.test_id=t.id JOIN users u ON u.group_id=tg.group_id WHERE u.id=? AND t.status='published' AND (t.start_date IS NULL OR t.start_date <= NOW()) AND (t.end_date IS NULL OR t.end_date >= NOW()) ORDER BY t.id DESC");
$stmt->execute([currentUserId()]);
$tests = $stmt->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="grid-2">
<?php foreach ($tests as $t): ?>
    <div class="card">
        <h2><?= htmlspecialchars($t['title']) ?></h2>
        <p><?= htmlspecialchars($t['description']) ?></p>
        <p><span class="badge"><?= htmlspecialchars($t['subject_name']) ?></span></p>
        <p>Время: <?= $t['time_limit'] ?> мин. | Попытки: <?= $t['max_attempts'] ?> | Проходной балл: <?= $t['passing_score'] ?>%</p>
        <a class="btn" href="/student/take-test.php?test_id=<?= $t['id'] ?>">Начать тест</a>
    </div>
<?php endforeach; ?>
<?php if (!$tests): ?><div class="card"><p>Пока нет доступных тестов.</p></div><?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
