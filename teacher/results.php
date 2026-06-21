<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/charts.php';
requireRole(['teacher']);
$pageTitle = 'Результаты студентов';
$stmt = $pdo->prepare("SELECT a.*, t.title, u.full_name, g.name AS group_name FROM test_attempts a JOIN tests t ON t.id=a.test_id JOIN users u ON u.id=a.student_id LEFT JOIN student_groups g ON g.id=u.group_id WHERE t.teacher_id=? ORDER BY a.id DESC");
$stmt->execute([currentUserId()]);
$rows = $stmt->fetchAll();
$avg = $rows ? round(array_sum(array_column($rows,'score')) / count($rows)) : 0;
$finished = count(array_filter($rows, function($r) { return $r['status'] === 'finished'; }));
$passed = count(array_filter($rows, function($r) { return $r['status'] === 'finished' && (int)$r['score'] >= 60; }));
$failed = max(0,$finished-$passed);
$byTestStmt=$pdo->prepare('SELECT t.title, COUNT(a.id) attempts, COALESCE(AVG(a.score),0) avg_score FROM tests t LEFT JOIN test_attempts a ON a.test_id=t.id AND a.status="finished" WHERE t.teacher_id=? GROUP BY t.id ORDER BY attempts DESC LIMIT 8');
$byTestStmt->execute([currentUserId()]); $byTest=$byTestStmt->fetchAll();
$byGroupStmt=$pdo->prepare('SELECT COALESCE(g.name,"Без группы") group_name, COALESCE(AVG(a.score),0) avg_score FROM test_attempts a JOIN tests t ON t.id=a.test_id JOIN users u ON u.id=a.student_id LEFT JOIN student_groups g ON g.id=u.group_id WHERE t.teacher_id=? AND a.status="finished" GROUP BY g.id ORDER BY avg_score DESC');
$byGroupStmt->execute([currentUserId()]); $byGroup=$byGroupStmt->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="kpi-row">
    <?= kpiCard('📊','Всего попыток',count($rows),'в базе') ?>
    <?= kpiCard('✅','Завершено',$finished,'готово') ?>
    <?= kpiCard('🎯','Средний балл',$avg.'%','по группе') ?>
    <?= kpiCard('🏆','Сдали',$passed,'успешно') ?>
</div>
<div class="grid grid-2" style="margin-bottom:18px">
    <div class="chart-card"><div class="chart-head"><div><h3>Средний балл по тестам</h3><p>Помогает быстро увидеть сложные и успешные тесты.</p></div></div><canvas class="stud-chart chart-canvas" data-type="bar" data-suffix="%" data-labels='<?= chartLabels($byTest,'title') ?>' data-values='<?= chartValues($byTest,'avg_score') ?>'></canvas></div>
    <div class="chart-card"><div class="chart-head"><div><h3>Проходимость</h3><p>Соотношение успешных и неуспешных завершённых попыток.</p></div></div><canvas class="stud-chart chart-canvas" data-type="donut" data-center="<?= $finished ?>" data-center-label="попыток" data-labels='<?= chartJson(['Сдали','Не сдали']) ?>' data-values='<?= chartJson([$passed,$failed]) ?>'></canvas></div>
</div>
<div class="grid grid-2" style="margin-bottom:18px">
    <div class="chart-card"><div class="chart-head"><div><h3>Средний балл по группам</h3><p>Сравнение результатов учебных групп.</p></div></div><canvas class="stud-chart chart-canvas chart-small" data-type="bar" data-suffix="%" data-labels='<?= chartLabels($byGroup,'group_name') ?>' data-values='<?= chartValues($byGroup,'avg_score') ?>'></canvas></div>
    <div class="card"><h3>Карточки последних результатов</h3><div class="grid grid-2"><?php foreach(array_slice($rows,0,4) as $r): ?><div class="result-card"><div class="score-ring" data-ring="<?= (int)$r['score'] ?>"><?= (int)$r['score'] ?>%</div><b><?= e($r['full_name']) ?></b><span style="color:var(--muted)"><?= e($r['title']) ?></span><div class="result-meta"><span class="badge <?= percentClass((int)$r['score']) ?>"><?= e(statusBadge($r['status'])) ?></span><span class="badge"><?= e($r['group_name'] ?? '-') ?></span></div></div><?php endforeach; ?></div></div>
</div>
<div class="table-toolbar"><h3>📋 Полная таблица</h3><a class="btn" href="/teacher/export-results.php">Экспорт CSV</a></div>
<div class="card table-wrap"><table><tr><th>Студент</th><th>Группа</th><th>Тест</th><th>Результат</th><th>Статус</th><th>Дата</th></tr><?php foreach ($rows as $r): ?><tr><td><?= e($r['full_name']) ?></td><td><?= e($r['group_name'] ?? '-') ?></td><td><?= e($r['title']) ?></td><td><span class="badge <?= percentClass((int)$r['score']) ?>"><?= (int)$r['score'] ?>%</span></td><td><span class="badge"><?= e(statusBadge($r['status'])) ?></span></td><td><?= e($r['started_at']) ?></td></tr><?php endforeach; ?></table></div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
