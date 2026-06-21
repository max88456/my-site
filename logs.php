<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
requireLogin();
$pageTitle = 'Журнал';

$isAdmin = currentUserRole() === 'admin';
if ($isAdmin) {
    $stmt = $pdo->query('SELECT l.*, u.full_name FROM activity_log l LEFT JOIN users u ON u.id=l.user_id ORDER BY l.id DESC LIMIT 150');
} else {
    $stmt = $pdo->prepare('SELECT l.*, u.full_name FROM activity_log l LEFT JOIN users u ON u.id=l.user_id WHERE l.user_id=? ORDER BY l.id DESC LIMIT 100');
    $stmt->execute([currentUserId()]);
}
$logs = $stmt->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<div class="card">
    <div class="table-toolbar">
        <div>
            <h3>🕘 <?= $isAdmin ? 'Системный журнал' : 'Мой журнал' ?></h3>
            <p class="muted-text"><?= $isAdmin ? 'История действий всех пользователей системы.' : 'История активности твоей учётной записи.' ?></p>
        </div>
        <span class="badge">Записей: <?= count($logs) ?></span>
    </div>
    <?php if (!$logs): ?>
        <div class="empty-state">Пока нет действий для отображения.</div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <tr>
                    <th>Дата</th>
                    <?php if ($isAdmin): ?><th>Пользователь</th><?php endif; ?>
                    <th>Действие</th>
                    <th>Описание</th>
                    <th>IP</th>
                </tr>
                <?php foreach ($logs as $l): ?>
                    <tr>
                        <td><?= e($l['created_at']) ?></td>
                        <?php if ($isAdmin): ?><td><?= e($l['full_name'] ?? '-') ?></td><?php endif; ?>
                        <td><b><?= e(actionLabel($l['action'])) ?></b></td>
                        <td><?= e($l['description']) ?></td>
                        <td><?= e($l['ip_address']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
