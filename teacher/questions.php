<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['teacher']);
$testId = (int)($_GET['test_id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM tests WHERE id=? AND teacher_id=?');
$stmt->execute([$testId, currentUserId()]);
$test = $stmt->fetch();
if (!$test) { die('Тест не найден'); }
$pageTitle = 'Вопросы: ' . $test['title'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $qText = trim($_POST['question_text']);
    $qType = $_POST['question_type'];
    $points = (int)$_POST['points'];
    $correctText = trim($_POST['correct_text_answer'] ?? '');
    $stmt = $pdo->prepare('INSERT INTO questions (test_id, question_text, question_type, points, correct_text_answer, position) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$testId, $qText, $qType, $points, $correctText ?: null, 0]);
    $questionId = $pdo->lastInsertId();

    if ($qType !== 'text') {
        foreach ($_POST['options'] ?? [] as $i => $opt) {
            $opt = trim($opt);
            if ($opt === '') continue;
            $isCorrect = in_array((string)$i, $_POST['correct'] ?? [], true) ? 1 : 0;
            $pdo->prepare('INSERT INTO question_options (question_id, option_text, is_correct, position) VALUES (?, ?, ?, ?)')->execute([$questionId, $opt, $isCorrect, $i]);
        }
    }
    logAction($pdo, currentUserId(), 'create_question', 'Добавлен вопрос в тест #' . $testId);
    header('Location: /teacher/questions.php?test_id=' . $testId); exit;
}

$q = $pdo->prepare('SELECT * FROM questions WHERE test_id=? ORDER BY id');
$q->execute([$testId]);
$questions = $q->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="table-toolbar"><h3>Вопросы теста</h3><a class="btn" href="/teacher/import-questions.php?test_id=<?= $testId ?>">📥 Импортировать готовые вопросы</a></div>
<div class="grid-2">
    <div class="card">
        <h2>Добавить вопрос</h2>
        <form method="post" class="form">
            <label>Текст вопроса <textarea name="question_text" required></textarea></label>
            <label>Тип вопроса
                <select name="question_type">
                    <option value="single">Один правильный ответ</option>
                    <option value="multiple">Несколько правильных ответов</option>
                    <option value="text">Текстовый ответ</option>
                </select>
            </label>
            <label>Баллы <input type="number" name="points" value="1" min="1"></label>
            <label>Варианты ответов</label>
            <?php for ($i=0; $i<4; $i++): ?>
                <div class="option"><input type="checkbox" name="correct[]" value="<?= $i ?>"><input name="options[]" placeholder="Вариант <?= $i+1 ?>"></div>
            <?php endfor; ?>
            <label>Правильный текстовый ответ <input name="correct_text_answer" placeholder="Например: .php"></label>
            <button class="btn">Добавить вопрос</button>
        </form>
    </div>
    <div class="card">
        <h2>Список вопросов</h2>
        <?php foreach ($questions as $item): ?>
            <div class="card" style="box-shadow:none;margin-bottom:10px;">
                <strong><?= htmlspecialchars($item['question_text']) ?></strong><br>
                <span class="badge"><?= $item['question_type'] ?></span> <?= $item['points'] ?> балл.
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
