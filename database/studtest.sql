CREATE DATABASE IF NOT EXISTS online_testing_students
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE online_testing_students;

DROP TABLE IF EXISTS question_imports;
DROP TABLE IF EXISTS attempt_answers;
DROP TABLE IF EXISTS test_attempts;
DROP TABLE IF EXISTS question_options;
DROP TABLE IF EXISTS questions;
DROP TABLE IF EXISTS test_groups;
DROP TABLE IF EXISTS tests;
DROP TABLE IF EXISTS subjects;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS activity_log;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS student_groups;
DROP TABLE IF EXISTS site_settings;

CREATE TABLE student_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    course INT NOT NULL,
    curator_name VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(150) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'teacher', 'student') NOT NULL DEFAULT 'student',
    group_id INT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    avatar VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_users_group FOREIGN KEY (group_id) REFERENCES student_groups(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    teacher_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_subject_teacher FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE tests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    time_limit INT NOT NULL DEFAULT 30,
    max_attempts INT NOT NULL DEFAULT 1,
    passing_score INT NOT NULL DEFAULT 50,
    shuffle_questions TINYINT(1) NOT NULL DEFAULT 1,
    shuffle_answers TINYINT(1) NOT NULL DEFAULT 1,
    status ENUM('draft', 'published', 'closed') NOT NULL DEFAULT 'draft',
    start_date DATETIME NULL,
    end_date DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_tests_subject FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
    CONSTRAINT fk_tests_teacher FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE test_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_id INT NOT NULL,
    group_id INT NOT NULL,
    CONSTRAINT fk_test_groups_test FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE,
    CONSTRAINT fk_test_groups_group FOREIGN KEY (group_id) REFERENCES student_groups(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_id INT NOT NULL,
    question_text TEXT NOT NULL,
    question_type ENUM('single', 'multiple', 'text') NOT NULL DEFAULT 'single',
    points INT NOT NULL DEFAULT 1,
    correct_text_answer VARCHAR(255) DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    position INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_questions_test FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE question_options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT NOT NULL,
    option_text TEXT NOT NULL,
    is_correct TINYINT(1) NOT NULL DEFAULT 0,
    position INT NOT NULL DEFAULT 0,
    CONSTRAINT fk_options_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE test_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    test_id INT NOT NULL,
    student_id INT NOT NULL,
    started_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    finished_at DATETIME NULL,
    score INT DEFAULT 0,
    total_points INT DEFAULT 0,
    earned_points INT DEFAULT 0,
    status ENUM('in_progress', 'finished', 'expired') NOT NULL DEFAULT 'in_progress',
    ip_address VARCHAR(50) DEFAULT NULL,
    CONSTRAINT fk_attempts_test FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE,
    CONSTRAINT fk_attempts_student FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE attempt_answers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    attempt_id INT NOT NULL,
    question_id INT NOT NULL,
    selected_option_id INT NULL,
    text_answer TEXT NULL,
    is_correct TINYINT(1) DEFAULT 0,
    earned_points INT DEFAULT 0,
    answered_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_attempt_answers_attempt FOREIGN KEY (attempt_id) REFERENCES test_attempts(id) ON DELETE CASCADE,
    CONSTRAINT fk_attempt_answers_question FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    CONSTRAINT fk_attempt_answers_option FOREIGN KEY (selected_option_id) REFERENCES question_options(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE question_imports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    teacher_id INT NOT NULL,
    test_id INT NOT NULL,
    source_type VARCHAR(30) NOT NULL,
    imported_count INT NOT NULL DEFAULT 0,
    skipped_count INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_import_teacher FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_import_test FOREIGN KEY (test_id) REFERENCES tests(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    is_read TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_notifications_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(255) NOT NULL,
    description TEXT,
    ip_address VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_activity_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE site_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO student_groups (name, course, curator_name) VALUES
('ПВТ-21', 2, 'Бубнов Андрей Вальеревич'),
('ИС-22', 2, 'Иванова Марина Сергеевна'),
('ПОВТ-31', 3, 'Серикбаев Нурлан Ермекович');

INSERT INTO users (full_name, email, password, role, group_id) VALUES
('Администратор системы', 'admin@test.kz', '$2y$10$huIaTXaqQ/8Ma8fEDMykKOrww22cKUtaP01buJyyT83MgTxGZfdgK', 'admin', NULL),
('Бубнов Андрей Вальеревич', 'teacher@test.kz', '$2y$10$huIaTXaqQ/8Ma8fEDMykKOrww22cKUtaP01buJyyT83MgTxGZfdgK', 'teacher', NULL),
('Базарбаев Рахат Адилбекович', 'student@test.kz', '$2y$10$huIaTXaqQ/8Ma8fEDMykKOrww22cKUtaP01buJyyT83MgTxGZfdgK', 'student', 1),
('Каишанов Мирас', 'miras@test.kz', '$2y$10$huIaTXaqQ/8Ma8fEDMykKOrww22cKUtaP01buJyyT83MgTxGZfdgK', 'student', 1);

INSERT INTO subjects (name, description, teacher_id) VALUES
('Основы программирования', 'Проверка базовых знаний по программированию.', 2),
('Базы данных', 'Проектирование и использование баз данных.', 2),
('Web-разработка', 'Разработка сайтов на PHP, HTML, CSS и JavaScript.', 2);

INSERT INTO tests (title, description, subject_id, teacher_id, time_limit, max_attempts, passing_score, status, start_date, end_date) VALUES
('Тест по основам PHP', 'Проверка знаний студентов по основам языка PHP.', 1, 2, 30, 2, 60, 'published', NOW(), DATE_ADD(NOW(), INTERVAL 30 DAY));

INSERT INTO test_groups (test_id, group_id) VALUES (1, 1), (1, 2);

INSERT INTO questions (test_id, question_text, question_type, points, correct_text_answer, position) VALUES
(1, 'Что означает аббревиатура PHP?', 'single', 1, NULL, 1),
(1, 'Какой символ используется для объявления переменной в PHP?', 'single', 1, NULL, 2),
(1, 'Какая функция используется для вывода текста в PHP?', 'single', 1, NULL, 3),
(1, 'Какие из перечисленных типов данных существуют в PHP?', 'multiple', 2, NULL, 4),
(1, 'Напишите расширение файла PHP.', 'text', 1, '.php', 5);

INSERT INTO question_options (question_id, option_text, is_correct, position) VALUES
(1, 'Personal Home Page / PHP: Hypertext Preprocessor', 1, 1),
(1, 'Private Hosting Protocol', 0, 2),
(1, 'Program High Page', 0, 3),
(1, 'Public HTML Processor', 0, 4),
(2, '$', 1, 1),
(2, '#', 0, 2),
(2, '@', 0, 3),
(2, '&', 0, 4),
(3, 'echo', 1, 1),
(3, 'printText', 0, 2),
(3, 'console.log', 0, 3),
(3, 'writeLine', 0, 4),
(4, 'integer', 1, 1),
(4, 'string', 1, 2),
(4, 'boolean', 1, 3),
(4, 'document', 0, 4);

INSERT INTO site_settings (setting_key, setting_value) VALUES
('site_name', 'StudTest'),
('site_description', 'Система онлайн-тестирования студентов'),
('default_theme', 'light'),
('allow_registration', '1');

-- Дополнительные демо-данные для расширенной версии
INSERT INTO tests (title, description, subject_id, teacher_id, time_limit, max_attempts, passing_score, status, start_date, end_date) VALUES
('SQL и проектирование БД', 'Нормализация, ключи, связи и базовые SELECT-запросы.', 2, 2, 25, 2, 65, 'published', NOW(), DATE_ADD(NOW(), INTERVAL 20 DAY)),
('HTML/CSS/JS: базовый frontend', 'Проверка основ вёрстки, CSS и JavaScript.', 3, 2, 20, 3, 60, 'published', NOW(), DATE_ADD(NOW(), INTERVAL 14 DAY));

INSERT INTO test_groups (test_id, group_id) VALUES (2,1),(2,3),(3,1),(3,2),(3,3);

INSERT INTO questions (test_id, question_text, question_type, points, correct_text_answer, position) VALUES
(2, 'Что такое первичный ключ?', 'single', 1, NULL, 1),
(2, 'Какая команда используется для получения данных?', 'single', 1, NULL, 2),
(2, 'Какие типы связей существуют в реляционных БД?', 'multiple', 2, NULL, 3),
(2, 'Как называется процесс уменьшения избыточности данных?', 'text', 1, 'нормализация', 4),
(3, 'Какой тег подключает CSS-файл?', 'single', 1, NULL, 1),
(3, 'Какие технологии относятся к frontend?', 'multiple', 2, NULL, 2),
(3, 'Какой язык выполняется в браузере?', 'single', 1, NULL, 3);

INSERT INTO question_options (question_id, option_text, is_correct, position) VALUES
(6, 'Уникальный идентификатор записи', 1, 1),(6, 'Любое текстовое поле', 0, 2),(6, 'Название таблицы', 0, 3),(6, 'Пароль пользователя', 0, 4),
(7, 'SELECT', 1, 1),(7, 'DELETE', 0, 2),(7, 'DROP', 0, 3),(7, 'ALTER', 0, 4),
(8, 'один-к-одному', 1, 1),(8, 'один-ко-многим', 1, 2),(8, 'многие-ко-многим', 1, 3),(8, 'цвет-к-размеру', 0, 4),
(10, 'link', 1, 1),(10, 'script', 0, 2),(10, 'style-src', 0, 3),(10, 'css', 0, 4),
(11, 'HTML', 1, 1),(11, 'CSS', 1, 2),(11, 'JavaScript', 1, 3),(11, 'MySQL Server', 0, 4),
(12, 'JavaScript', 1, 1),(12, 'PHP', 0, 2),(12, 'SQL', 0, 3),(12, 'Apache', 0, 4);

INSERT INTO notifications (user_id, title, message) VALUES
(3, 'Добро пожаловать в StudTest', 'Вам доступны тесты по PHP, SQL и frontend-разработке.'),
(4, 'Новый тест опубликован', 'Преподаватель добавил тест SQL и проектирование БД.'),
(2, 'Статистика готова', 'В разделе аналитики можно посмотреть средний балл и активность студентов.');


-- Демо-результаты для карточек, графиков и диаграмм
INSERT INTO test_attempts (test_id, student_id, started_at, finished_at, score, total_points, earned_points, status, ip_address) VALUES
(1, 3, DATE_SUB(NOW(), INTERVAL 9 DAY), DATE_SUB(NOW(), INTERVAL 9 DAY), 82, 6, 5, 'finished', '127.0.0.1'),
(2, 3, DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_SUB(NOW(), INTERVAL 7 DAY), 74, 5, 4, 'finished', '127.0.0.1'),
(3, 3, DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 91, 4, 4, 'finished', '127.0.0.1'),
(1, 4, DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY), 64, 6, 4, 'finished', '127.0.0.1'),
(2, 4, DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), 58, 5, 3, 'finished', '127.0.0.1'),
(3, 4, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 88, 4, 4, 'finished', '127.0.0.1'),
(1, 3, DATE_SUB(NOW(), INTERVAL 1 DAY), NULL, 0, 0, 0, 'in_progress', '127.0.0.1');

INSERT INTO question_imports (teacher_id, test_id, source_type, imported_count, skipped_count, created_at) VALUES
(2, 1, 'csv', 12, 0, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(2, 2, 'json', 8, 1, DATE_SUB(NOW(), INTERVAL 2 DAY));
