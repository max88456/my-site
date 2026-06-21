<?php
require_once __DIR__.'/../config/database.php'; require_once __DIR__.'/../includes/auth.php'; requireRole(['student']);
$pageTitle='Календарь тестов';
$s=$pdo->prepare('SELECT DISTINCT t.*, sub.name subject_name FROM tests t JOIN subjects sub ON sub.id=t.subject_id JOIN test_groups tg ON tg.test_id=t.id JOIN users u ON u.group_id=tg.group_id WHERE u.id=? AND t.status="published" ORDER BY COALESCE(t.end_date,t.created_at) ASC'); $s->execute([currentUserId()]); $tests=$s->fetchAll();
include __DIR__.'/../includes/header.php';
?>
<div class="card"><h3>📅 Ближайшие дедлайны</h3><div class="timeline"><?php foreach($tests as $t): ?><div class="timeline-item"><div class="timeline-dot">📌</div><div><b><?= e($t['title']) ?></b><p style="margin:5px 0;color:var(--muted)"><?= e($t['subject_name']) ?> · до <?= e($t['end_date'] ?: 'без срока') ?> · <?= (int)$t['time_limit'] ?> мин.</p><a class="btn btn-ghost" href="/student/take-test.php?id=<?= (int)$t['id'] ?>">Пройти</a></div></div><?php endforeach; ?></div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
