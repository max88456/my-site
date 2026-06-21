<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);
$pageTitle = 'Журнал действий';
$logs = $pdo->query('SELECT l.*, u.full_name FROM activity_log l LEFT JOIN users u ON u.id=l.user_id ORDER BY l.id DESC LIMIT 100')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="card table-wrap">
<table><tr><th>Дата</th><th>Пользователь</th><th>Действие</th><th>Описание</th><th>IP</th></tr>
<?php foreach ($logs as $l): ?><tr><td><?= e($l['created_at']) ?></td><td><?= e($l['full_name'] ?? '-') ?></td><td><?= e(actionLabel($l['action'])) ?></td><td><?= e($l['description']) ?></td><td><?= e($l['ip_address']) ?></td></tr><?php endforeach; ?>
</table>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
