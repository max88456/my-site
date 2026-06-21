<?php
require_once __DIR__.'/../config/database.php'; require_once __DIR__.'/../includes/auth.php'; require_once __DIR__.'/../includes/charts.php'; requireRole(['admin']);
$pageTitle='Аналитика системы';
function scalar(PDO $pdo,string $sql,array $p=[]): int {$s=$pdo->prepare($sql);$s->execute($p);return (int)$s->fetchColumn();}
$users=scalar($pdo,'SELECT COUNT(*) FROM users'); $tests=scalar($pdo,'SELECT COUNT(*) FROM tests'); $attempts=scalar($pdo,'SELECT COUNT(*) FROM test_attempts'); $avg=scalar($pdo,'SELECT COALESCE(AVG(score),0) FROM test_attempts WHERE status="finished"');
$byRole=$pdo->query('SELECT role, COUNT(*) c FROM users GROUP BY role')->fetchAll();
$byTest=$pdo->query('SELECT t.title, COUNT(a.id) attempts, COALESCE(AVG(a.score),0) avg_score FROM tests t LEFT JOIN test_attempts a ON a.test_id=t.id AND a.status="finished" GROUP BY t.id ORDER BY attempts DESC LIMIT 10')->fetchAll();
$subjects=$pdo->query('SELECT s.name, COUNT(t.id) tests_count FROM subjects s LEFT JOIN tests t ON t.subject_id=s.id GROUP BY s.id ORDER BY tests_count DESC')->fetchAll();
$daily=$pdo->query('SELECT DATE(started_at) day, COUNT(*) c, COALESCE(AVG(score),0) avg_score FROM test_attempts GROUP BY DATE(started_at) ORDER BY day ASC LIMIT 14')->fetchAll();
$groups=$pdo->query('SELECT COALESCE(g.name,"Без группы") group_name, COUNT(u.id) students FROM student_groups g LEFT JOIN users u ON u.group_id=g.id AND u.role="student" GROUP BY g.id')->fetchAll();
include __DIR__.'/../includes/header.php';
?>
<div class="kpi-row"><?= kpiCard('👥','Пользователей',$users,'в системе') ?><?= kpiCard('🧪','Тестов',$tests,'создано') ?><?= kpiCard('🚀','Попыток',$attempts,'всего') ?><?= kpiCard('🎯','Средний балл',$avg.'%','по платформе') ?></div>
<div class="grid grid-2" style="margin-bottom:18px">
 <div class="chart-card"><div class="chart-head"><div><h3>Активность прохождения</h3><p>Количество попыток по датам.</p></div></div><canvas class="stud-chart chart-canvas" data-type="line" data-labels='<?= chartLabels($daily,'day') ?>' data-values='<?= chartValues($daily,'c') ?>'></canvas></div>
 <div class="chart-card"><div class="chart-head"><div><h3>Роли пользователей</h3><p>Структура аккаунтов в системе.</p></div></div><canvas class="stud-chart chart-canvas" data-type="donut" data-center="<?= $users ?>" data-center-label="users" data-labels='<?= chartJson(array_map(function($r) { return roleLabel($r['role']); }, $byRole)) ?>' data-values='<?= chartValues($byRole,'c') ?>'></canvas></div>
</div>
<div class="grid grid-2" style="margin-bottom:18px">
 <div class="chart-card"><div class="chart-head"><div><h3>Средний балл по тестам</h3><p>Рейтинг тестов по результативности.</p></div></div><canvas class="stud-chart chart-canvas" data-type="bar" data-suffix="%" data-labels='<?= chartLabels($byTest,'title') ?>' data-values='<?= chartValues($byTest,'avg_score') ?>'></canvas></div>
 <div class="chart-card"><div class="chart-head"><div><h3>Студенты по группам</h3><p>Наполнение учебных групп.</p></div></div><canvas class="stud-chart chart-canvas" data-type="bar" data-labels='<?= chartLabels($groups,'group_name') ?>' data-values='<?= chartValues($groups,'students') ?>'></canvas></div>
</div>
<div class="grid grid-2">
 <div class="card"><h3>📚 Предметы и тесты</h3><?php foreach($subjects as $s): ?><p><b><?= e($s['name']) ?></b><span style="float:right"><?= (int)$s['tests_count'] ?> тест.</span></p><div class="progress"><span data-width="<?= min(100,(int)$s['tests_count']*25) ?>%"></span></div><?php endforeach; ?></div>
 <div class="card"><h3>📊 Таблица эффективности</h3><div class="table-wrap"><table><tr><th>Тест</th><th>Попытки</th><th>Средний балл</th></tr><?php foreach($byTest as $t): ?><tr><td><?= e($t['title']) ?></td><td><?= (int)$t['attempts'] ?></td><td><span class="badge <?= percentClass((int)$t['avg_score']) ?>"><?= (int)$t['avg_score'] ?>%</span></td></tr><?php endforeach; ?></table></div></div>
</div>
<?php include __DIR__.'/../includes/footer.php'; ?>
