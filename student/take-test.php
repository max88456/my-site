<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['student']);
$testId = (int)($_GET['test_id'] ?? ($_GET['id'] ?? 0));

$stmt = $pdo->prepare("SELECT t.* FROM tests t JOIN test_groups tg ON tg.test_id=t.id JOIN users u ON u.group_id=tg.group_id WHERE t.id=? AND u.id=? AND t.status='published' LIMIT 1");
$stmt->execute([$testId, currentUserId()]);
$test = $stmt->fetch();
if (!$test) die('Тест недоступен');

$attemptsStmt = $pdo->prepare("SELECT COUNT(*) FROM test_attempts WHERE test_id=? AND student_id=? AND status='finished'");
$attemptsStmt->execute([$testId, currentUserId()]);
if ((int)$attemptsStmt->fetchColumn() >= (int)$test['max_attempts']) die('Количество попыток исчерпано');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();
    $pdo->prepare('INSERT INTO test_attempts (test_id, student_id, status, ip_address) VALUES (?, ?, ?, ?)')->execute([$testId, currentUserId(), 'in_progress', $_SERVER['REMOTE_ADDR'] ?? null]);
    $attemptId = $pdo->lastInsertId();

    $qStmt = $pdo->prepare('SELECT * FROM questions WHERE test_id=?');
    $qStmt->execute([$testId]);
    $questions = $qStmt->fetchAll();
    $totalPoints = 0;
    $earnedPoints = 0;

    foreach ($questions as $q) {
        $totalPoints += (int)$q['points'];
        $qid = (int)$q['id'];
        $earned = 0;

        if ($q['question_type'] === 'single') {
            $selected = (int)($_POST['answer'][$qid] ?? 0);
            $check = $pdo->prepare('SELECT is_correct FROM question_options WHERE id=? AND question_id=?');
            $check->execute([$selected, $qid]);
            $isCorrect = (int)($check->fetchColumn() ?? 0);
            $earned = $isCorrect ? (int)$q['points'] : 0;
            $pdo->prepare('INSERT INTO attempt_answers (attempt_id, question_id, selected_option_id, is_correct, earned_points) VALUES (?, ?, ?, ?, ?)')->execute([$attemptId, $qid, $selected ?: null, $isCorrect, $earned]);
        } elseif ($q['question_type'] === 'multiple') {
            $selected = array_map('intval', $_POST['answer'][$qid] ?? []);
            $correctStmt = $pdo->prepare('SELECT id FROM question_options WHERE question_id=? AND is_correct=1 ORDER BY id');
            $correctStmt->execute([$qid]);
            $correct = array_map('intval', $correctStmt->fetchAll(PDO::FETCH_COLUMN));
            sort($selected); sort($correct);
            $isCorrect = ($selected === $correct) ? 1 : 0;
            $earned = $isCorrect ? (int)$q['points'] : 0;
            foreach ($selected as $optId) {
                $pdo->prepare('INSERT INTO attempt_answers (attempt_id, question_id, selected_option_id, is_correct, earned_points) VALUES (?, ?, ?, ?, ?)')->execute([$attemptId, $qid, $optId, $isCorrect, $earned]);
            }
            if (!$selected) {
                $pdo->prepare('INSERT INTO attempt_answers (attempt_id, question_id, is_correct, earned_points) VALUES (?, ?, 0, 0)')->execute([$attemptId, $qid]);
            }
        } else {
            $text = trim($_POST['answer'][$qid] ?? '');
            $correctText = trim((string)($q['correct_text_answer'] ?? ''));
            $isCorrect = ($correctText !== '' && mb_strtolower($text) === mb_strtolower($correctText)) ? 1 : 0;
            $earned = $isCorrect ? (int)$q['points'] : 0;
            $pdo->prepare('INSERT INTO attempt_answers (attempt_id, question_id, text_answer, is_correct, earned_points) VALUES (?, ?, ?, ?, ?)')->execute([$attemptId, $qid, $text, $isCorrect, $earned]);
        }
        $earnedPoints += $earned;
    }

    $score = $totalPoints > 0 ? round(($earnedPoints / $totalPoints) * 100) : 0;
    $pdo->prepare("UPDATE test_attempts SET finished_at=NOW(), score=?, total_points=?, earned_points=?, status='finished' WHERE id=?")->execute([$score, $totalPoints, $earnedPoints, $attemptId]);
    logAction($pdo, currentUserId(), 'finish_test', 'Завершён тест #' . $testId . ', результат ' . $score . '%');
    $pdo->commit();
    header('Location: /student/result.php?attempt_id=' . $attemptId); exit;
}

$qStmt = $pdo->prepare('SELECT * FROM questions WHERE test_id=? ORDER BY position, id');
$qStmt->execute([$testId]);
$questions = $qStmt->fetchAll();
foreach ($questions as &$q) {
    $o = $pdo->prepare('SELECT * FROM question_options WHERE question_id=? ORDER BY position, id');
    $o->execute([$q['id']]);
    $q['options'] = $o->fetchAll();
}
unset($q);
$pageTitle = 'Прохождение теста';
include __DIR__ . '/../includes/header.php';
?>
<div class="card" style="margin-bottom:18px;display:flex;justify-content:space-between;align-items:center;gap:16px;">
    <div><h2><?= htmlspecialchars($test['title']) ?></h2><p><?= htmlspecialchars($test['description']) ?></p></div>
    <div class="timer" id="timer">00:00</div>
</div>
<form method="post" id="testForm">
<?php foreach ($questions as $index => $q): ?>
    <div class="card question-box">
        <h3><?= $index + 1 ?>. <?= htmlspecialchars($q['question_text']) ?></h3>
        <?php if ($q['question_type'] === 'single'): ?>
            <?php foreach ($q['options'] as $opt): ?>
                <label class="option"><input type="radio" name="answer[<?= $q['id'] ?>]" value="<?= $opt['id'] ?>"> <?= htmlspecialchars($opt['option_text']) ?></label>
            <?php endforeach; ?>
        <?php elseif ($q['question_type'] === 'multiple'): ?>
            <?php foreach ($q['options'] as $opt): ?>
                <label class="option"><input type="checkbox" name="answer[<?= $q['id'] ?>][]" value="<?= $opt['id'] ?>"> <?= htmlspecialchars($opt['option_text']) ?></label>
            <?php endforeach; ?>
        <?php else: ?>
            <input name="answer[<?= $q['id'] ?>]" placeholder="Введите ответ">
        <?php endif; ?>
    </div>
<?php endforeach; ?>
<button class="btn btn-success" type="submit">Завершить тест</button>
</form>
<script>startTimer(<?= (int)$test['time_limit'] * 60 ?>, 'testForm');</script>
<?php include __DIR__ . '/../includes/footer.php'; ?>
