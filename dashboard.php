<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/charts.php';
requireLogin();
$pageTitle = 'Главная панель';
$role = currentUserRole();
$stats = [];
function scalarQuery(PDO $pdo, string $sql, array $params=[]): int { $s=$pdo->prepare($sql); $s->execute($params); return (int)$s->fetchColumn(); }
if ($role === 'admin') {
    $stats = [
        'Пользователей'=>scalarQuery($pdo,'SELECT COUNT(*) FROM users'),
        'Тестов'=>scalarQuery($pdo,'SELECT COUNT(*) FROM tests'),
        'Попыток'=>scalarQuery($pdo,'SELECT COUNT(*) FROM test_attempts'),
        'Групп'=>scalarQuery($pdo,'SELECT COUNT(*) FROM student_groups'),
    ];
} elseif ($role === 'teacher') {
    $stats = [
        'Мои тесты'=>scalarQuery($pdo,'SELECT COUNT(*) FROM tests WHERE teacher_id=?',[currentUserId()]),
        'Вопросы'=>scalarQuery($pdo,'SELECT COUNT(*) FROM questions q JOIN tests t ON t.id=q.test_id WHERE t.teacher_id=?',[currentUserId()]),
        'Попытки'=>scalarQuery($pdo,'SELECT COUNT(*) FROM test_attempts a JOIN tests t ON t.id=a.test_id WHERE t.teacher_id=?',[currentUserId()]),
        'Средний балл'=>scalarQuery($pdo,'SELECT COALESCE(AVG(score),0) FROM test_attempts a JOIN tests t ON t.id=a.test_id WHERE t.teacher_id=? AND a.status="finished"',[currentUserId()]),
    ];
} else {
    $stats = [
        'Доступные тесты'=>scalarQuery($pdo,'SELECT COUNT(DISTINCT t.id) FROM tests t JOIN test_groups tg ON tg.test_id=t.id JOIN users u ON u.group_id=tg.group_id WHERE u.id=? AND t.status="published"',[currentUserId()]),
        'Пройдено'=>scalarQuery($pdo,'SELECT COUNT(*) FROM test_attempts WHERE student_id=? AND status="finished"',[currentUserId()]),
        'Средний балл'=>scalarQuery($pdo,'SELECT COALESCE(AVG(score),0) FROM test_attempts WHERE student_id=? AND status="finished"',[currentUserId()]),
        'Сертификаты'=>scalarQuery($pdo,'SELECT COUNT(*) FROM test_attempts a JOIN tests t ON t.id=a.test_id WHERE a.student_id=? AND a.status="finished" AND a.score>=t.passing_score',[currentUserId()]),
    ];
}
$top = $pdo->query('SELECT u.full_name, COALESCE(AVG(a.score),0) avg_score, COUNT(a.id) done FROM users u LEFT JOIN test_attempts a ON a.student_id=u.id AND a.status="finished" WHERE u.role="student" GROUP BY u.id ORDER BY avg_score DESC LIMIT 5')->fetchAll();
$trend = $pdo->query('SELECT DATE(started_at) day, COUNT(*) c, COALESCE(AVG(score),0) avg_score FROM test_attempts GROUP BY DATE(started_at) ORDER BY day ASC LIMIT 14')->fetchAll();
$testChart = $pdo->query('SELECT t.title, COUNT(a.id) attempts, COALESCE(AVG(a.score),0) avg_score FROM tests t LEFT JOIN test_attempts a ON a.test_id=t.id AND a.status="finished" GROUP BY t.id ORDER BY attempts DESC LIMIT 8')->fetchAll();
include __DIR__ . '/includes/header.php';
?>
<div class="hero card">
    <h2>Привет, <?= e(currentUserName()) ?> 👋</h2>
    <p>Это расширенная версия StudTest: роли, тесты, вопросы, результаты, уведомления, аналитика, сертификаты, экспорт и визуальный dashboard.</p>
    <div class="hero-actions">
        <?php if ($role==='admin'): ?><a class="btn btn-light" href="/admin/analytics.php">Открыть аналитику</a><a class="btn btn-light" href="/admin/users.php">Пользователи</a><?php endif; ?>
        <?php if ($role==='teacher'): ?><a class="btn btn-light" href="/teacher/test-create.php">Создать тест</a><a class="btn btn-light" href="/teacher/results.php">Результаты</a><?php endif; ?>
        <?php if ($role==='student'): ?><a class="btn btn-light" href="/student/tests.php">Начать тест</a><a class="btn btn-light" href="/student/results.php">Мои баллы</a><?php endif; ?>
    </div>
</div>
<div class="grid grid-4" style="margin-top:18px">
<?php foreach($stats as $label=>$value): ?>
    <div class="stat"><div class="num"><?= e($value) ?></div><div class="label"><?= e($label) ?></div><div class="progress" style="margin-top:14px"><span data-width="<?= min(100,(int)$value*10 ?: 8) ?>%"></span></div></div>
<?php endforeach; ?>
</div>

<div class="grid grid-2" style="margin-top:18px">
    <div class="chart-card">
        <div class="chart-head"><div><h3>📈 Активность платформы</h3><p>Попытки прохождения тестов по датам.</p></div></div>
        <canvas class="stud-chart chart-canvas" data-type="line" data-labels='<?= chartLabels($trend,"day") ?>' data-values='<?= chartValues($trend,"c") ?>'></canvas>
    </div>
    <div class="chart-card">
        <div class="chart-head"><div><h3>🧪 Популярные тесты</h3><p>Сколько раз студенты запускали тесты.</p></div></div>
        <canvas class="stud-chart chart-canvas" data-type="bar" data-labels='<?= chartLabels($testChart,"title") ?>' data-values='<?= chartValues($testChart,"attempts") ?>'></canvas>
    </div>
</div>

<div class="grid grid-2" style="margin-top:18px">
    <div class="card"><h3>🏆 Топ студентов</h3><div class="table-wrap"><table><tr><th>Студент</th><th>Средний балл</th><th>Тестов</th></tr><?php foreach($top as $r): ?><tr><td><?= e($r['full_name']) ?></td><td><span class="badge <?= percentClass((int)$r['avg_score']) ?>"><?= (int)$r['avg_score'] ?>%</span></td><td><?= (int)$r['done'] ?></td></tr><?php endforeach; ?></table></div></div>
    <div class="card">
        <h3>🚀 Быстрые действия</h3>
        <p style="color:var(--muted);margin-top:0">Главная страница теперь без служебного журнала — только полезные переходы и метрики.</p>
        <div class="quick-actions">
            <a class="quick-action" href="/profile.php"><b>👤 Профиль</b><span>Личные данные и история активности</span></a>
            <?php if ($role==='admin'): ?><a class="quick-action" href="/admin/analytics.php"><b>📊 Аналитика</b><span>Графики, роли, тесты и динамика</span></a><?php endif; ?>
            <?php if ($role==='teacher'): ?><a class="quick-action" href="/teacher/import-questions.php"><b>📥 Импорт вопросов</b><span>CSV, TXT и JSON-шаблоны</span></a><?php endif; ?>
            <?php if ($role==='student'): ?><a class="quick-action" href="/student/tests.php"><b>🧪 Доступные тесты</b><span>Перейти к прохождению</span></a><?php endif; ?>
            <a class="quick-action" href="/notifications.php"><b>🔔 Уведомления</b><span>Важные сообщения системы</span></a>
        </div>
    </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
