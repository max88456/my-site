<?php
require_once __DIR__.'/config/database.php'; require_once __DIR__.'/includes/auth.php'; requireLogin();
$pageTitle='Уведомления';
$pdo->prepare('UPDATE notifications SET is_read=1 WHERE user_id=?')->execute([currentUserId()]);
$s=$pdo->prepare('SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC LIMIT 50'); $s->execute([currentUserId()]); $items=$s->fetchAll();
include __DIR__.'/includes/header.php';
?>
<div class="card"><h3>🔔 Центр уведомлений</h3><?php if(!$items): ?><p style="color:var(--muted)">Уведомлений пока нет. Когда преподаватель создаст тест или появится результат, они будут здесь.</p><?php endif; ?><div class="timeline"><?php foreach($items as $n): ?><div class="timeline-item"><div class="timeline-dot">🔔</div><div><b><?= e($n['title']) ?></b><p style="color:var(--muted);margin:5px 0"><?= e($n['message']) ?></p><small><?= e($n['created_at']) ?></small></div></div><?php endforeach; ?></div></div>
<?php include __DIR__.'/includes/footer.php'; ?>
