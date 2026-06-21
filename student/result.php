<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['student']);
$attemptId = (int)($_GET['attempt_id'] ?? 0);
$stmt = $pdo->prepare('SELECT a.*, t.title, t.passing_score FROM test_attempts a JOIN tests t ON t.id=a.test_id WHERE a.id=? AND a.student_id=?');
$stmt->execute([$attemptId, currentUserId()]);
$result = $stmt->fetch();
if (!$result) die('Результат не найден');
$pageTitle = 'Результат теста';
include __DIR__ . '/../includes/header.php';
?>
<div class="card" style="text-align:center;">
    <h2><?= htmlspecialchars($result['title']) ?></h2>
    <div style="font-size:72px;font-weight:900;color:#2563eb;"><?= $result['score'] ?>%</div>
    <p>Набрано баллов: <?= $result['earned_points'] ?> из <?= $result['total_points'] ?></p>
    <?php if ($result['score'] >= $result['passing_score']): ?>
        <div class="alert alert-success">Тест успешно пройден</div>
    <?php else: ?>
        <div class="alert alert-error">Тест не пройден</div>
    <?php endif; ?>
    <a class="btn" href="/student/results.php">Мои результаты</a>
</div>
<div style="margin-top:18px"><a class="btn" href="/student/certificates.php">Мои сертификаты</a> <a class="btn btn-ghost" href="/student/results.php">Все результаты</a></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
