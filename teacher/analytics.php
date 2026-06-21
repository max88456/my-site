<?php
require_once __DIR__.'/../config/database.php'; require_once __DIR__.'/../includes/auth.php'; require_once __DIR__.'/../includes/charts.php'; requireRole(['teacher']);
$pageTitle='Аналитика преподавателя';
$uid=currentUserId();
function qv(PDO $pdo,string $sql,array $p=[]){$s=$pdo->prepare($sql);$s->execute($p);return $s->fetchAll();}
$k_tests=(int)$pdo->prepare('SELECT COUNT(*) FROM tests WHERE teacher_id=?')->execute([$uid]);
$st=$pdo->prepare('SELECT COUNT(*) FROM tests WHERE teacher_id=?');$st->execute([$uid]);$tests=(int)$st->fetchColumn();
$st=$pdo->prepare('SELECT COUNT(*) FROM questions q JOIN tests t ON t.id=q.test_id WHERE t.teacher_id=?');$st->execute([$uid]);$questions=(int)$st->fetchColumn();
$st=$pdo->prepare('SELECT COUNT(*) FROM test_attempts a JOIN tests t ON t.id=a.test_id WHERE t.teacher_id=?');$st->execute([$uid]);$attempts=(int)$st->fetchColumn();
$st=$pdo->prepare('SELECT COALESCE(AVG(score),0) FROM test_attempts a JOIN tests t ON t.id=a.test_id WHERE t.teacher_id=? AND a.status="finished"');$st->execute([$uid]);$avg=(int)$st->fetchColumn();
$testsChart=qv($pdo,'SELECT t.title, COUNT(a.id) attempts, COALESCE(AVG(a.score),0) avg_score FROM tests t LEFT JOIN test_attempts a ON a.test_id=t.id AND a.status="finished" WHERE t.teacher_id=? GROUP BY t.id ORDER BY t.id DESC LIMIT 10',[$uid]);
$typeChart=qv($pdo,'SELECT q.question_type, COUNT(*) c FROM questions q JOIN tests t ON t.id=q.test_id WHERE t.teacher_id=? GROUP BY q.question_type',[$uid]);
$daily=qv($pdo,'SELECT DATE(a.started_at) day, COUNT(*) c, COALESCE(AVG(a.score),0) avg_score FROM test_attempts a JOIN tests t ON t.id=a.test_id WHERE t.teacher_id=? GROUP BY DATE(a.started_at) ORDER BY day ASC LIMIT 14',[$uid]);
include __DIR__.'/../includes/header.php';
?>
<div class="kpi-row"><?= kpiCard('🧪','Тесты',$tests,'создано') ?><?= kpiCard('❓','Вопросы',$questions,'в банке') ?><?= kpiCard('🚀','Попытки',$attempts,'активность') ?><?= kpiCard('🎯','Средний балл',$avg.'%','качество') ?></div>
<div class="grid grid-2" style="margin-bottom:18px">
<div class="chart-card"><div class="chart-head"><div><h3>Динамика попыток</h3><p>Количество прохождений по дням.</p></div></div><canvas class="stud-chart chart-canvas" data-type="line" data-labels='<?= chartLabels($daily,'day') ?>' data-values='<?= chartValues($daily,'c') ?>'></canvas></div>
<div class="chart-card"><div class="chart-head"><div><h3>Средний балл по тестам</h3><p>Быстрый контроль сложности.</p></div></div><canvas class="stud-chart chart-canvas" data-type="bar" data-suffix="%" data-labels='<?= chartLabels($testsChart,'title') ?>' data-values='<?= chartValues($testsChart,'avg_score') ?>'></canvas></div>
</div>
<div class="grid grid-2"><div class="chart-card"><div class="chart-head"><div><h3>Типы вопросов</h3><p>Баланс single, multiple и text.</p></div></div><canvas class="stud-chart chart-canvas" data-type="donut" data-center="<?= $questions ?>" data-center-label="вопросов" data-labels='<?= chartLabels($typeChart,'question_type') ?>' data-values='<?= chartValues($typeChart,'c') ?>'></canvas></div><div class="card"><h3>Что можно улучшить</h3><div class="timeline"><div class="timeline-item"><div class="timeline-dot">1</div><div><b>Добавьте больше multiple-вопросов</b><p style="color:var(--muted);margin:4px 0">Они лучше проверяют глубокое понимание темы.</p></div></div><div class="timeline-item"><div class="timeline-dot">2</div><div><b>Сравнивайте средний балл</b><p style="color:var(--muted);margin:4px 0">Если тест ниже 50%, возможно вопросы слишком сложные.</p></div></div><div class="timeline-item"><div class="timeline-dot">3</div><div><b>Импортируйте готовые наборы</b><p style="color:var(--muted);margin:4px 0">Используйте страницу импорта для быстрого наполнения тестов.</p></div></div></div></div></div>
<?php include __DIR__.'/../includes/footer.php'; ?>
