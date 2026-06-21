<?php
require_once __DIR__.'/../config/database.php'; require_once __DIR__.'/../includes/auth.php'; requireRole(['teacher']);
$pageTitle='Банк вопросов';
$q=$pdo->prepare('SELECT q.*, t.title test_title FROM questions q JOIN tests t ON t.id=q.test_id WHERE t.teacher_id=? ORDER BY q.created_at DESC'); $q->execute([currentUserId()]); $items=$q->fetchAll();
include __DIR__.'/../includes/header.php';
?>
<div class="card"><h3>🏦 Все вопросы преподавателя</h3><p style="color:var(--muted)">Здесь удобно проверять весь набор вопросов по вашим тестам.</p><div class="table-wrap"><table><tr><th>ID</th><th>Тест</th><th>Вопрос</th><th>Тип</th><th>Баллы</th></tr><?php foreach($items as $i): ?><tr><td>#<?= (int)$i['id'] ?></td><td><?= e($i['test_title']) ?></td><td><?= e(mb_strimwidth($i['question_text'],0,85,'...')) ?></td><td><span class="badge"><?= e($i['question_type']) ?></span></td><td><?= (int)$i['points'] ?></td></tr><?php endforeach; ?></table></div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
