<?php
require_once __DIR__.'/config/database.php';
require_once __DIR__.'/includes/auth.php';
requireLogin();
$pageTitle = 'Справочная система';
$role = currentUserRole();
include __DIR__.'/includes/header.php';
?>
<div class="help-hero glass">
    <div>
        <span class="badge">StudTest Help Center</span>
        <h2>Справочная система платформы</h2>
        <p>Быстрые инструкции по работе с онлайн-тестированием: пользователи, группы, предметы, тесты, импорт вопросов, результаты, аналитика и профиль.</p>
    </div>
    <div class="help-search-card">
        <label>Поиск по справке</label>
        <input id="helpSearch" type="text" placeholder="Например: импорт, группа, результат, тест..." oninput="filterHelp()">
    </div>
</div>

<div class="grid grid-3 help-quick">
    <?php if ($role === 'admin'): ?>
        <a class="help-tile" href="#admin"><span>🛡️</span><b>Администратору</b><small>Пользователи, группы, предметы, журнал</small></a>
    <?php endif; ?>
    <?php if ($role === 'teacher' || $role === 'admin'): ?>
        <a class="help-tile" href="#teacher"><span>👨‍🏫</span><b>Преподавателю</b><small>Создание тестов, вопросы, импорт, результаты</small></a>
    <?php endif; ?>
    <a class="help-tile" href="#student"><span>🎓</span><b>Студенту</b><small>Прохождение тестов, таймер, сертификаты</small></a>
    <a class="help-tile" href="#faq"><span>💬</span><b>FAQ</b><small>Частые вопросы и быстрые решения</small></a>
</div>

<?php if ($role === 'admin'): ?>
<div class="card help-section" id="admin" data-help="администратор пользователи группы предметы журнал блокировка удаление изменение">
    <h3>🛡️ Раздел администратора</h3>
    <div class="help-steps">
        <div><b>Пользователи</b><p>Откройте «Пользователи», чтобы добавить нового пользователя, изменить данные, заблокировать/разблокировать аккаунт или удалить запись.</p></div>
        <div><b>Группы</b><p>В разделе «Группы» можно создать группу, изменить название, курс и куратора, а также удалить неиспользуемую группу.</p></div>
        <div><b>Предметы</b><p>В разделе «Предметы» добавляются дисциплины. Для каждого предмета можно назначить преподавателя и изменить описание.</p></div>
        <div><b>Журнал</b><p>Журнал показывает ключевые действия пользователей: вход, выход, завершение теста, импорт вопросов, изменения данных.</p></div>
    </div>
</div>
<?php endif; ?>

<?php if ($role === 'teacher' || $role === 'admin'): ?>
<div class="card help-section" id="teacher" data-help="преподаватель тест вопросы импорт csv json txt результаты аналитика">
    <h3>👨‍🏫 Раздел преподавателя</h3>
    <div class="help-steps">
        <div><b>Создание теста</b><p>Откройте «Создать тест», заполните название, предмет, лимит времени, количество попыток, проходной балл и статус публикации.</p></div>
        <div><b>Добавление вопросов</b><p>В тест можно добавить одиночный выбор, множественный выбор или текстовый ответ. Для вариантов ответа отметьте правильные.</p></div>
        <div><b>Импорт вопросов</b><p>Откройте «Импорт вопросов», выберите тест и загрузите CSV, TXT или JSON-файл с готовыми вопросами.</p></div>
        <div><b>Результаты</b><p>Раздел «Результаты» показывает попытки студентов, баллы, проценты, статусы и даёт возможность экспортировать данные.</p></div>
    </div>
    <div class="format-box help-format">CSV пример:
question;type;points;option1;option2;option3;option4;correct
Что такое PHP?;single;1;Язык программирования;ОС;Браузер;СУБД;1
Типы PHP;multiple;2;integer;string;boolean;document;1|2|3</div>
</div>
<?php endif; ?>

<div class="card help-section" id="student" data-help="студент пройти тест таймер результаты сертификаты календарь">
    <h3>🎓 Раздел студента</h3>
    <div class="help-steps">
        <div><b>Доступные тесты</b><p>На странице «Доступные тесты» отображаются опубликованные тесты, назначенные вашей группе.</p></div>
        <div><b>Прохождение теста</b><p>Во время прохождения работает таймер. После завершения система автоматически подсчитывает результат.</p></div>
        <div><b>Мои результаты</b><p>В разделе «Мои результаты» отображаются баллы, процент выполнения, дата и статус прохождения.</p></div>
        <div><b>Сертификаты</b><p>Если тест пройден успешно, сертификат появляется в соответствующем разделе.</p></div>
    </div>
</div>

<div class="card help-section" id="faq" data-help="faq ошибки пароль вход база данных openserver пустая страница вопросы">
    <h3>💬 Частые вопросы</h3>
    <div class="faq-list">
        <details open>
            <summary>Не могу войти в систему</summary>
            <p>Проверьте email и пароль. Демо-доступ: <code>admin@test.kz</code>, <code>teacher@test.kz</code>, <code>student@test.kz</code>. Пароль: <code>password</code>.</p>
        </details>
        <details>
            <summary>Страница стала пустой</summary>
            <p>Обычно это ошибка PHP. Проверьте версию PHP в OpenServer и включите отображение ошибок. Проект адаптирован под PHP 7.x и выше.</p>
        </details>
        <details>
            <summary>Как импортировать вопросы?</summary>
            <p>Перейдите в «Импорт вопросов», выберите тест, загрузите файл CSV/TXT/JSON и нажмите «Импортировать».</p>
        </details>
        <details>
            <summary>Почему студент не видит тест?</summary>
            <p>Проверьте, что тест опубликован, назначен группе студента и дата прохождения находится в разрешённом интервале.</p>
        </details>
    </div>
</div>

<script>
function filterHelp(){
    var q = document.getElementById('helpSearch').value.toLowerCase();
    var sections = document.querySelectorAll('.help-section');
    for (var i=0;i<sections.length;i++) {
        var text = (sections[i].innerText + ' ' + (sections[i].getAttribute('data-help') || '')).toLowerCase();
        sections[i].style.display = text.indexOf(q) !== -1 ? '' : 'none';
    }
}
</script>
<?php include __DIR__.'/includes/footer.php'; ?>
