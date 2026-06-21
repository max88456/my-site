<?php
require_once __DIR__.'/../config/database.php'; require_once __DIR__.'/../includes/auth.php'; requireRole(['student']);
$pageTitle='Сертификаты';
$s=$pdo->prepare('SELECT a.*, t.title, t.passing_score FROM test_attempts a JOIN tests t ON t.id=a.test_id WHERE a.student_id=? AND a.status="finished" AND a.score>=t.passing_score ORDER BY a.finished_at DESC'); $s->execute([currentUserId()]); $items=$s->fetchAll();
include __DIR__.'/../includes/header.php';
?>
<div class="grid grid-2"><?php foreach($items as $c): ?><div class="certificate"><h2>Certificate</h2><p>подтверждает, что</p><h3><?= e(currentUserName()) ?></h3><p>успешно прошёл(ла) тест <b><?= e($c['title']) ?></b></p><div class="badge success">Результат <?= (int)$c['score'] ?>%</div><p><?= e($c['finished_at']) ?></p><button class="btn btn-ghost" onclick="window.print()">Печать</button></div><?php endforeach; ?><?php if(!$items): ?><div class="card"><h3>Пока нет сертификатов</h3><p style="color:var(--muted)">Наберите проходной балл по тесту — сертификат появится здесь.</p></div><?php endif; ?></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
