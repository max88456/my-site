<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../includes/auth.php'; require_once __DIR__ . '/../includes/charts.php'; requireRole(['student']);
$pageTitle='Мои результаты';
$stmt=$pdo->prepare('SELECT a.*, t.title, t.passing_score, s.name subject_name FROM test_attempts a JOIN tests t ON t.id=a.test_id JOIN subjects s ON s.id=t.subject_id WHERE a.student_id=? ORDER BY a.id DESC');
$stmt->execute([currentUserId()]); $rows=$stmt->fetchAll();
$finished=array_values(array_filter($rows, function($r) { return $r['status'] === 'finished'; }));
$avg=$finished?round(array_sum(array_column($finished,'score'))/count($finished)):0; $best=$finished?max(array_column($finished,'score')):0; $passed=count(array_filter($finished, function($r) { return (int)$r['score'] >= (int)$r['passing_score']; }));
$trend=array_reverse(array_slice($finished,0,10));
$subjects=[]; foreach($finished as $r){$subjects[$r['subject_name']][]=(int)$r['score'];} $subRows=[]; foreach($subjects as $name=>$vals){$subRows[]=['name'=>$name,'avg'=>round(array_sum($vals)/count($vals))];}
include __DIR__ . '/../includes/header.php';
?>
<div class="kpi-row"><?= kpiCard('📝','Пройдено',count($finished),'тестов') ?><?= kpiCard('🎯','Средний балл',$avg.'%','личный') ?><?= kpiCard('🏆','Лучший балл',$best.'%','рекорд') ?><?= kpiCard('🎖️','Сертификаты',$passed,'доступно') ?></div>
<div class="grid grid-2" style="margin-bottom:18px">
    <div class="chart-card"><div class="chart-head"><div><h3>Динамика моих баллов</h3><p>Последние завершённые тесты.</p></div></div><canvas class="stud-chart chart-canvas" data-type="line" data-suffix="%" data-labels='<?= chartLabels($trend,'title') ?>' data-values='<?= chartValues($trend,'score') ?>'></canvas></div>
    <div class="chart-card"><div class="chart-head"><div><h3>Средний балл по предметам</h3><p>Где ты сильнее всего.</p></div></div><canvas class="stud-chart chart-canvas" data-type="bar" data-suffix="%" data-labels='<?= chartLabels($subRows,'name') ?>' data-values='<?= chartValues($subRows,'avg') ?>'></canvas></div>
</div>
<div class="card"><h3>Карточки результатов</h3><?php if(!$rows): ?><div class="empty-state">Результатов пока нет. Пройди первый тест 🚀</div><?php else: ?><div class="grid grid-3"><?php foreach($rows as $r): ?><div class="result-card"><div class="score-ring" data-ring="<?= (int)$r['score'] ?>"><?= (int)$r['score'] ?>%</div><b><?= e($r['title']) ?></b><span style="color:var(--muted)"><?= e($r['subject_name']) ?> · <?= e($r['started_at']) ?></span><div class="result-meta"><span class="badge <?= percentClass((int)$r['score']) ?>"><?= (int)$r['score'] >= (int)$r['passing_score'] ? 'Сдан' : 'Не сдан' ?></span><span class="badge"><?= e(statusBadge($r['status'])) ?></span></div></div><?php endforeach; ?></div><?php endif; ?></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
