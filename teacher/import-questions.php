<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../config/gemini.php';
requireRole(['teacher']);
checkCsrf();
$pageTitle = 'Импорт вопросов';

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS question_imports (
        id INT AUTO_INCREMENT PRIMARY KEY,
        teacher_id INT NOT NULL,
        test_id INT NOT NULL,
        source_type VARCHAR(30) NOT NULL,
        imported_count INT NOT NULL DEFAULT 0,
        skipped_count INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} catch (Throwable $e) {}

function normalizeText($v){ return trim((string)$v); }
function detectDelimiter($line) { $candidates=array(";",",","\t"); $best=';'; $bestCount=0; foreach($candidates as $d){$count=substr_count($line,$d); if($count>$bestCount){$best=$d;$bestCount=$count;}} return $best; }

function extractJsonArray($text) {
    $text = trim($text);
    $text = preg_replace('/^```(?:json)?/i', '', $text);
    $text = preg_replace('/```$/', '', trim($text));
    $start = strpos($text, '[');
    $end = strrpos($text, ']');
    if ($start !== false && $end !== false && $end > $start) {
        return substr($text, $start, $end - $start + 1);
    }
    return $text;
}

function callGeminiApi($apiKey, $model, $topic, $count, $difficulty, $type) {
    $apiKey = trim($apiKey);
    if ($apiKey === '' && defined('GEMINI_API_KEY')) {
        $apiKey = trim(GEMINI_API_KEY);
    }
    if ($apiKey === 'PASTE_YOUR_GEMINI_API_KEY_HERE') {
        $apiKey = '';
    }
    $model = trim($model);
    if ($model === '' && defined('GEMINI_MODEL')) {
        $model = GEMINI_MODEL;
    }
    if ($model === '') {
        $model = 'gemini-1.5-flash';
    }
    $count = max(1, min(50, (int)$count));
    $difficulty = trim($difficulty) ?: 'средний';
    $type = trim($type) ?: 'mixed';
    if ($apiKey === '') throw new Exception('Gemini API ключ не указан. Откройте файл config/gemini.php и вставьте ключ в GEMINI_API_KEY.');
    if (trim($topic) === '') throw new Exception('Введите тему для генерации вопросов.');

    $prompt = "Сгенерируй {$count} вопросов для онлайн-тестирования студентов на русском языке.\n" .
        "Тема: {$topic}.\nСложность: {$difficulty}.\nТип вопросов: {$type}.\n" .
        "Верни ТОЛЬКО валидный JSON-массив без markdown. Каждый объект строго в формате:\n" .
        "{\"question\":\"текст вопроса\",\"type\":\"single|multiple|text\",\"points\":1,\"option1\":\"...\",\"option2\":\"...\",\"option3\":\"...\",\"option4\":\"...\",\"option5\":\"\",\"option6\":\"\",\"correct\":\"1\"}\n" .
        "Для single укажи один правильный номер в correct. Для multiple укажи номера через |, например 1|3. Для text оставь варианты пустыми, а в correct напиши короткий правильный ответ.";

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . rawurlencode($model) . ':generateContent?key=' . urlencode($apiKey);
    $payload = json_encode(array(
        'contents' => array(array('parts' => array(array('text' => $prompt)))),
        'generationConfig' => array('temperature' => 0.6, 'responseMimeType' => 'application/json')
    ), JSON_UNESCAPED_UNICODE);

    if (!function_exists('curl_init')) {
        throw new Exception('В PHP не включён модуль cURL. Включите cURL в OpenServer или используйте обычный импорт CSV/JSON.');
    }
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $response = curl_exec($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);
    if ($response === false || $response === '') throw new Exception('Gemini не ответил: ' . $err);
    $decoded = json_decode($response, true);
    if ($http >= 400) {
        $msg = isset($decoded['error']['message']) ? $decoded['error']['message'] : $response;
        throw new Exception('Ошибка Gemini API: ' . $msg);
    }
    $text = '';
    if (isset($decoded['candidates'][0]['content']['parts'][0]['text'])) $text = $decoded['candidates'][0]['content']['parts'][0]['text'];
    if ($text === '') throw new Exception('Gemini вернул пустой ответ.');
    $json = extractJsonArray($text);
    $data = json_decode($json, true);
    if (!is_array($data)) throw new Exception('Ответ Gemini не удалось распознать как JSON. Скопируйте ответ вручную и проверьте формат.');
    return $data;
}

function importQuestion(PDO $pdo, $testId, array $item) {
    $question = normalizeText(isset($item['question']) ? $item['question'] : (isset($item['question_text']) ? $item['question_text'] : (isset($item[0]) ? $item[0] : '')));
    if ($question === '') return false;
    $type = normalizeText(isset($item['type']) ? $item['type'] : (isset($item['question_type']) ? $item['question_type'] : (isset($item[1]) ? $item[1] : 'single')));
    if (!in_array($type, array('single','multiple','text'), true)) $type='single';
    $points = max(1, (int)(isset($item['points']) ? $item['points'] : (isset($item[2]) ? $item[2] : 1)));
    $correctRaw = normalizeText(isset($item['correct']) ? $item['correct'] : (isset($item['correct_text_answer']) ? $item['correct_text_answer'] : (isset($item[9]) ? $item[9] : '')));
    $pdo->prepare('INSERT INTO questions (test_id, question_text, question_type, points, correct_text_answer, position) VALUES (?, ?, ?, ?, ?, 0)')
        ->execute(array($testId, $question, $type, $points, $type==='text' ? $correctRaw : null));
    $qid = (int)$pdo->lastInsertId();
    if ($type !== 'text') {
        $options=array();
        for($i=1;$i<=6;$i++){
            $val = isset($item['option'.$i]) ? $item['option'.$i] : (isset($item['answer'.$i]) ? $item['answer'.$i] : (isset($item[2+$i]) ? $item[2+$i] : ''));
            $val = normalizeText($val);
            if ($val !== '') $options[$i]=$val;
        }
        if (count($options) < 2) return false;
        $correctParts = preg_split('/[|;,+]/', $correctRaw);
        foreach($options as $i=>$opt){
            $ok = false;
            foreach($correctParts as $c){
                $c=trim($c);
                if ($c !== '' && ((string)$i === $c || mb_strtolower($opt, 'UTF-8') === mb_strtolower($c, 'UTF-8'))) {$ok=true; break;}
            }
            $pdo->prepare('INSERT INTO question_options (question_id, option_text, is_correct, position) VALUES (?, ?, ?, ?)')->execute(array($qid, $opt, $ok?1:0, $i));
        }
    }
    return true;
}

$testsStmt = $pdo->prepare('SELECT t.id, t.title, s.name subject_name FROM tests t JOIN subjects s ON s.id=t.subject_id WHERE t.teacher_id=? ORDER BY t.id DESC');
$testsStmt->execute(array(currentUserId()));
$tests = $testsStmt->fetchAll();
$preselectedTestId = (int)(isset($_GET['test_id']) ? $_GET['test_id'] : 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mode = isset($_POST['mode']) ? $_POST['mode'] : 'file';
    $testId=(int)(isset($_POST['test_id']) ? $_POST['test_id'] : 0);
    $check=$pdo->prepare('SELECT id FROM tests WHERE id=? AND teacher_id=?'); $check->execute(array($testId,currentUserId()));
    if (!$check->fetch()) { flash('danger','Выберите свой тест для импорта.'); header('Location: /teacher/import-questions.php'); exit; }

    $imported=0; $skipped=0; $sourceType='csv';
    try {
        if ($mode === 'ai') {
            $data = callGeminiApi(
                '',
                defined('GEMINI_MODEL') ? GEMINI_MODEL : 'gemini-1.5-flash',
                isset($_POST['ai_topic']) ? $_POST['ai_topic'] : '',
                isset($_POST['ai_count']) ? $_POST['ai_count'] : 10,
                isset($_POST['ai_difficulty']) ? $_POST['ai_difficulty'] : 'средний',
                isset($_POST['ai_type']) ? $_POST['ai_type'] : 'mixed'
            );
            foreach($data as $item){ if(is_array($item) && importQuestion($pdo,$testId,$item)) $imported++; else $skipped++; }
            $sourceType='gemini_ai';
            logAction($pdo,currentUserId(),'ai_import_questions',"Gemini: импортировано {$imported} вопросов в тест #{$testId}");
            flash('success',"ИИ-импорт завершён: добавлено {$imported}, пропущено {$skipped}.");
            try { $pdo->prepare('INSERT INTO question_imports (teacher_id,test_id,source_type,imported_count,skipped_count) VALUES (?,?,?,?,?)')->execute(array(currentUserId(),$testId,$sourceType,$imported,$skipped)); } catch(Throwable $e) {}
            header('Location: /teacher/questions.php?test_id='.$testId); exit;
        } else {
            $format=isset($_POST['format']) ? $_POST['format'] : 'csv';
            $raw=trim(isset($_POST['questions_data']) ? $_POST['questions_data'] : '');
            if (!empty($_FILES['questions_file']['tmp_name'])) $raw=file_get_contents($_FILES['questions_file']['tmp_name']);
            if ($raw === '') { flash('warning','Вставьте вопросы или загрузите файл.'); header('Location: /teacher/import-questions.php'); exit; }
            if ($format === 'json') {
                $data=json_decode($raw,true);
                if (!is_array($data)) { flash('danger','JSON не распознан. Проверьте формат.'); header('Location: /teacher/import-questions.php'); exit; }
                foreach($data as $item){ if(is_array($item) && importQuestion($pdo,$testId,$item)) $imported++; else $skipped++; }
            } else {
                $lines=preg_split('/\r\n|\r|\n/', trim($raw));
                $delimiter = detectDelimiter(isset($lines[0]) ? $lines[0] : ';');
                $header = str_getcsv(array_shift($lines), $delimiter);
                $lower = array_map(function($h) { return mb_strtolower(trim($h), 'UTF-8'); }, $header);
                $hasHeader = in_array('question', $lower, true) || in_array('question_text', $lower, true);
                if (!$hasHeader) { array_unshift($lines, implode($delimiter,$header)); $lower=array(); }
                foreach($lines as $line){
                    if(trim($line)==='') continue;
                    $cols=str_getcsv($line,$delimiter);
                    $item=$cols;
                    if ($hasHeader) { $item=array(); foreach($lower as $i=>$name) $item[$name]=isset($cols[$i]) ? $cols[$i] : ''; }
                    if(importQuestion($pdo,$testId,$item)) $imported++; else $skipped++;
                }
            }
            $sourceType=$format;
            try { $pdo->prepare('INSERT INTO question_imports (teacher_id,test_id,source_type,imported_count,skipped_count) VALUES (?,?,?,?,?)')->execute(array(currentUserId(),$testId,$sourceType,$imported,$skipped)); } catch(Throwable $e) {}
            logAction($pdo,currentUserId(),'import_questions',"Импортировано {$imported} вопросов в тест #{$testId}");
            flash('success',"Импорт завершён: добавлено {$imported}, пропущено {$skipped}.");
            header('Location: /teacher/questions.php?test_id='.$testId); exit;
        }
    } catch (Throwable $e) {
        flash('danger', $e->getMessage());
        header('Location: /teacher/import-questions.php?test_id='.$testId); exit;
    }
}

$history=array(); try { $h=$pdo->prepare('SELECT qi.*, t.title FROM question_imports qi JOIN tests t ON t.id=qi.test_id WHERE qi.teacher_id=? ORDER BY qi.id DESC LIMIT 8'); $h->execute(array(currentUserId())); $history=$h->fetchAll(); } catch(Throwable $e) {}
include __DIR__ . '/../includes/header.php';
?>
<div class="grid grid-2">
    <div class="card">
        <div class="chart-head"><div><h3>🤖 Импорт вопросов через Gemini AI</h3><p>Выберите тест, тему и количество — система сама создаст вопросы и добавит их в выбранный тест.</p></div></div>
        <form method="post" class="form">
            <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
            <input type="hidden" name="mode" value="ai">
            <label>Тест
                <select name="test_id" required>
                    <option value="">Выберите тест</option>
                    <?php foreach($tests as $t): ?><option value="<?= (int)$t['id'] ?>" <?= $preselectedTestId===(int)$t['id'] ? 'selected' : '' ?>><?= e($t['title']) ?> — <?= e($t['subject_name']) ?></option><?php endforeach; ?>
                </select>
            </label>
            <div class="grid grid-2">
                <label>Модель
                    <input value="<?= e(defined('GEMINI_MODEL') ? GEMINI_MODEL : 'gemini-1.5-flash') ?>" disabled>
                </label>
                <label>Количество вопросов
                    <input type="number" name="ai_count" min="1" max="50" value="10">
                </label>
            </div>
            <label>Тема для генерации
                <textarea name="ai_topic" rows="4" placeholder="Например: Основы PHP: переменные, массивы, функции, формы, GET/POST" required></textarea>
            </label>
            <div class="grid grid-2">
                <label>Сложность
                    <select name="ai_difficulty"><option>лёгкий</option><option selected>средний</option><option>сложный</option></select>
                </label>
                <label>Тип вопросов
                    <select name="ai_type"><option value="mixed">Смешанные</option><option value="single">Один правильный ответ</option><option value="multiple">Несколько правильных ответов</option><option value="text">Текстовые ответы</option></select>
                </label>
            </div>
            <p class="muted-text">API ключ берётся из файла <b>config/gemini.php</b>. На странице он не отображается.</p>
            <button class="btn">✨ Сгенерировать и импортировать</button>
        </form>
    </div>

    <div class="card">
        <div class="chart-head"><div><h3>📥 Импорт из файла или текста</h3><p>CSV, TXT или JSON — для готовых вопросов от преподавателя.</p></div></div>
        <form method="post" enctype="multipart/form-data" class="form">
            <input type="hidden" name="csrf" value="<?= csrfToken() ?>">
            <input type="hidden" name="mode" value="file">
            <label>Тест
                <select name="test_id" required>
                    <option value="">Выберите тест</option>
                    <?php foreach($tests as $t): ?><option value="<?= (int)$t['id'] ?>" <?= $preselectedTestId===(int)$t['id'] ? 'selected' : '' ?>><?= e($t['title']) ?> — <?= e($t['subject_name']) ?></option><?php endforeach; ?>
                </select>
            </label>
            <label>Формат
                <select name="format"><option value="csv">CSV / TXT</option><option value="json">JSON</option></select>
            </label>
            <div class="import-drop">
                <label>Файл с вопросами <input type="file" name="questions_file" accept=".csv,.txt,.json"></label>
                <p style="color:var(--muted);margin-bottom:0">Можно не загружать файл, а просто вставить данные ниже.</p>
            </div>
            <label>Данные для импорта
<textarea name="questions_data" rows="8" placeholder="question;type;points;option1;option2;option3;option4;option5;option6;correct"></textarea></label>
            <button class="btn">Импортировать вопросы</button>
        </form>
    </div>
</div>

<div class="grid grid-3" style="margin-top:18px">
    <div class="card">
        <h3>✅ CSV-шаблон</h3>
        <div class="format-box">question;type;points;option1;option2;option3;option4;option5;option6;correct<br>Что такое PHP?;single;1;Язык программирования;ОС;Браузер;СУБД;;;1<br>Какие типы есть в PHP?;multiple;2;integer;string;boolean;document;;;1|2|3<br>Расширение PHP-файла;text;1;;;;;;; .php</div>
    </div>
    <div class="card">
        <h3>🧩 JSON-шаблон</h3>
        <div class="format-box">[
  {"question":"Что такое HTML?","type":"single","points":1,"option1":"Язык разметки","option2":"База данных","correct":"1"},
  {"question":"Frontend технологии","type":"multiple","points":2,"option1":"HTML","option2":"CSS","option3":"JavaScript","correct":"1|2|3"}
]</div>
    </div>
    <div class="card"><h3>🕘 История импорта</h3><?php if(!$history): ?><div class="empty-state">Импортов пока нет</div><?php else: ?><div class="table-wrap"><table><tr><th>Тест</th><th>Источник</th><th>Добавлено</th><th>Дата</th></tr><?php foreach($history as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= e($r['source_type']==='gemini_ai' ? 'Gemini AI' : $r['source_type']) ?></td><td><?= (int)$r['imported_count'] ?></td><td><?= e($r['created_at']) ?></td></tr><?php endforeach; ?></table></div><?php endif; ?></div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
