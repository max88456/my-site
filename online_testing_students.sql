-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 16 2026 г., 05:26
-- Версия сервера: 10.6.9-MariaDB
-- Версия PHP: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `online_testing_students`
--

-- --------------------------------------------------------

--
-- Структура таблицы `activity_log`
--

CREATE TABLE `activity_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `activity_log`
--

INSERT INTO `activity_log` (`id`, `user_id`, `action`, `description`, `ip_address`, `created_at`) VALUES
(1, 1, 'login', 'Вход в систему', '127.0.0.1', '2026-06-16 01:00:18'),
(2, 1, 'logout', 'Выход из системы', '127.0.0.1', '2026-06-16 01:00:40'),
(3, 3, 'login', 'Вход в систему', '127.0.0.1', '2026-06-16 01:00:46'),
(4, 3, 'finish_test', 'Завершён тест #1, результат 17%', '127.0.0.1', '2026-06-16 01:00:55'),
(5, 3, 'logout', 'Выход из системы', '127.0.0.1', '2026-06-16 01:00:59'),
(6, 3, 'login', 'Вход в систему', '127.0.0.1', '2026-06-16 01:01:43'),
(7, 3, 'logout', 'Выход из системы', '127.0.0.1', '2026-06-16 01:15:30'),
(8, 1, 'Вход в систему', 'Пользователь авторизовался', '127.0.0.1', '2026-06-16 01:15:38'),
(9, 1, 'toggle_user', 'Изменён статус пользователя #4', '127.0.0.1', '2026-06-16 01:15:49'),
(10, 1, 'toggle_user', 'Изменён статус пользователя #4', '127.0.0.1', '2026-06-16 01:15:51'),
(11, 1, 'toggle_user', 'Изменён статус пользователя #4', '127.0.0.1', '2026-06-16 01:15:52'),
(12, 1, 'toggle_user', 'Изменён статус пользователя #4', '127.0.0.1', '2026-06-16 01:15:57'),
(13, 1, 'toggle_user', 'Изменён статус пользователя #4', '127.0.0.1', '2026-06-16 01:18:14'),
(14, 1, 'toggle_user', 'Изменён статус пользователя #4', '127.0.0.1', '2026-06-16 01:18:15'),
(15, 1, 'toggle_user', 'Изменён статус пользователя #4', '127.0.0.1', '2026-06-16 01:20:23'),
(16, 1, 'toggle_user', 'Изменён статус пользователя #4', '127.0.0.1', '2026-06-16 01:20:25'),
(17, 1, 'update_user', 'Обновлён пользователь #4', '127.0.0.1', '2026-06-16 01:20:36'),
(18, 1, 'update_group', 'Изменена группа #2', '127.0.0.1', '2026-06-16 01:20:57'),
(19, 1, 'update_group', 'Изменена группа #1', '127.0.0.1', '2026-06-16 01:21:07'),
(20, 1, 'logout', 'Выход из системы', '127.0.0.1', '2026-06-16 01:22:28'),
(21, 1, 'Вход в систему', 'Пользователь авторизовался', '127.0.0.1', '2026-06-16 01:25:55'),
(22, 1, 'logout', 'Выход из системы', '127.0.0.1', '2026-06-16 01:27:20'),
(23, 2, 'Вход в систему', 'Пользователь авторизовался', '127.0.0.1', '2026-06-16 01:27:26'),
(24, 2, 'ai_import_questions', 'Gemini: импортировано 10 вопросов в тест #1', '127.0.0.1', '2026-06-16 01:38:12'),
(25, 2, 'logout', 'Выход из системы', '127.0.0.1', '2026-06-16 01:38:29'),
(26, 3, 'Вход в систему', 'Пользователь авторизовался', '127.0.0.1', '2026-06-16 01:38:34'),
(27, 3, 'finish_test', 'Завершён тест #1, результат 13%', '127.0.0.1', '2026-06-16 01:39:02'),
(28, 3, 'logout', 'Выход из системы', '127.0.0.1', '2026-06-16 01:39:08'),
(29, 5, 'register', 'Новый студент зарегистрировался', '127.0.0.1', '2026-06-16 01:42:07'),
(30, 5, 'Вход в систему', 'Пользователь авторизовался', '127.0.0.1', '2026-06-16 01:42:18'),
(31, 5, 'logout', 'Выход из системы', '127.0.0.1', '2026-06-16 01:45:13'),
(32, 1, 'Вход в систему', 'Пользователь авторизовался', '127.0.0.1', '2026-06-16 02:16:36'),
(33, 1, 'logout', 'Выход из системы', '127.0.0.1', '2026-06-16 02:26:41');

-- --------------------------------------------------------

--
-- Структура таблицы `attempt_answers`
--

CREATE TABLE `attempt_answers` (
  `id` int(11) NOT NULL,
  `attempt_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `selected_option_id` int(11) DEFAULT NULL,
  `text_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `earned_points` int(11) DEFAULT 0,
  `answered_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `attempt_answers`
--

INSERT INTO `attempt_answers` (`id`, `attempt_id`, `question_id`, `selected_option_id`, `text_answer`, `is_correct`, `earned_points`, `answered_at`) VALUES
(1, 1, 1, 1, NULL, 1, 1, '2026-06-16 01:00:55'),
(2, 1, 2, 8, NULL, 0, 0, '2026-06-16 01:00:55'),
(3, 1, 3, 11, NULL, 0, 0, '2026-06-16 01:00:55'),
(4, 1, 4, 15, NULL, 0, 0, '2026-06-16 01:00:55'),
(5, 1, 5, NULL, 'dddd', 0, 0, '2026-06-16 01:00:55'),
(6, 2, 1, 3, NULL, 0, 0, '2026-06-16 01:39:02'),
(7, 2, 2, 6, NULL, 0, 0, '2026-06-16 01:39:02'),
(8, 2, 3, 12, NULL, 0, 0, '2026-06-16 01:39:02'),
(9, 2, 4, 13, NULL, 0, 0, '2026-06-16 01:39:02'),
(10, 2, 4, 14, NULL, 0, 0, '2026-06-16 01:39:02'),
(11, 2, 5, NULL, 'f', 0, 0, '2026-06-16 01:39:02'),
(12, 2, 6, 18, NULL, 1, 1, '2026-06-16 01:39:02'),
(13, 2, 7, 24, NULL, 0, 0, '2026-06-16 01:39:02'),
(14, 2, 8, NULL, 'efdfd', 0, 0, '2026-06-16 01:39:02'),
(15, 2, 9, 27, NULL, 1, 1, '2026-06-16 01:39:02'),
(16, 2, 10, 31, NULL, 0, 0, '2026-06-16 01:39:02'),
(17, 2, 10, 33, NULL, 0, 0, '2026-06-16 01:39:02'),
(18, 2, 10, 34, NULL, 0, 0, '2026-06-16 01:39:02'),
(19, 2, 11, NULL, 'ddd', 0, 0, '2026-06-16 01:39:02'),
(20, 2, 12, 37, NULL, 0, 0, '2026-06-16 01:39:02'),
(21, 2, 13, 43, NULL, 0, 0, '2026-06-16 01:39:02'),
(22, 2, 14, 48, NULL, 0, 0, '2026-06-16 01:39:02'),
(23, 2, 15, NULL, 'df', 0, 0, '2026-06-16 01:39:02');

-- --------------------------------------------------------

--
-- Структура таблицы `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Структура таблицы `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('single','multiple','text') NOT NULL DEFAULT 'single',
  `points` int(11) NOT NULL DEFAULT 1,
  `correct_text_answer` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `questions`
--

INSERT INTO `questions` (`id`, `test_id`, `question_text`, `question_type`, `points`, `correct_text_answer`, `image`, `position`, `created_at`) VALUES
(1, 1, 'Что означает аббревиатура PHP?', 'single', 1, NULL, NULL, 1, '2026-06-16 01:00:05'),
(2, 1, 'Какой символ используется для объявления переменной в PHP?', 'single', 1, NULL, NULL, 2, '2026-06-16 01:00:05'),
(3, 1, 'Какая функция используется для вывода текста в PHP?', 'single', 1, NULL, NULL, 3, '2026-06-16 01:00:05'),
(4, 1, 'Какие из перечисленных типов данных существуют в PHP?', 'multiple', 2, NULL, NULL, 4, '2026-06-16 01:00:05'),
(5, 1, 'Напишите расширение файла PHP.', 'text', 1, '.php', NULL, 5, '2026-06-16 01:00:05'),
(6, 1, 'Какой будет результат выполнения следующего кода? $x = 5; function myFunc() { echo $x; } myFunc();', 'single', 1, NULL, NULL, 0, '2026-06-16 01:38:11'),
(7, 1, 'Какие из следующих функций используются для сортировки массивов в PHP? (Выберите все подходящие варианты)', 'multiple', 1, NULL, NULL, 0, '2026-06-16 01:38:11'),
(8, 1, 'Какая директива используется для включения строгой типизации в PHP? Напишите её полностью.', 'text', 1, 'declare(strict_types=1);', NULL, 0, '2026-06-16 01:38:11'),
(9, 1, 'В чем разница между операторами == и === в PHP?', 'single', 1, NULL, NULL, 0, '2026-06-16 01:38:11'),
(10, 1, 'Какие из перечисленных переменных являются суперглобальными массивами в PHP?', 'multiple', 1, NULL, NULL, 0, '2026-06-16 01:38:11'),
(11, 1, 'Какой оператор используется для конкатенации (объединения) строк в PHP?', 'text', 1, '.', NULL, 0, '2026-06-16 01:38:11'),
(12, 1, 'Какую функцию необходимо вызвать перед использованием сессий в PHP скрипте?', 'single', 1, NULL, NULL, 0, '2026-06-16 01:38:11'),
(13, 1, 'Какие типы данных относятся к скалярным типам в PHP?', 'multiple', 1, NULL, NULL, 0, '2026-06-16 01:38:11'),
(14, 1, 'Что произойдет при объединении двух ассоциативных массивов с помощью оператора + ($a + $b), если у них есть одинаковые строковые ключи?', 'single', 1, NULL, NULL, 0, '2026-06-16 01:38:12'),
(15, 1, 'Как называется встроенная функция PHP, которая возвращает тип переменной в виде строки?', 'text', 1, 'gettype', NULL, 0, '2026-06-16 01:38:12');

-- --------------------------------------------------------

--
-- Структура таблицы `question_imports`
--

CREATE TABLE `question_imports` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `source_type` varchar(30) NOT NULL,
  `imported_count` int(11) NOT NULL DEFAULT 0,
  `skipped_count` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `question_imports`
--

INSERT INTO `question_imports` (`id`, `teacher_id`, `test_id`, `source_type`, `imported_count`, `skipped_count`, `created_at`) VALUES
(1, 2, 1, 'gemini_ai', 10, 0, '2026-06-16 01:38:12');

-- --------------------------------------------------------

--
-- Структура таблицы `question_options`
--

CREATE TABLE `question_options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `option_text` text NOT NULL,
  `is_correct` tinyint(1) NOT NULL DEFAULT 0,
  `position` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `question_options`
--

INSERT INTO `question_options` (`id`, `question_id`, `option_text`, `is_correct`, `position`) VALUES
(1, 1, 'Personal Home Page / PHP: Hypertext Preprocessor', 1, 1),
(2, 1, 'Private Hosting Protocol', 0, 2),
(3, 1, 'Program High Page', 0, 3),
(4, 1, 'Public HTML Processor', 0, 4),
(5, 2, '$', 1, 1),
(6, 2, '#', 0, 2),
(7, 2, '@', 0, 3),
(8, 2, '&', 0, 4),
(9, 3, 'echo', 1, 1),
(10, 3, 'printText', 0, 2),
(11, 3, 'console.log', 0, 3),
(12, 3, 'writeLine', 0, 4),
(13, 4, 'integer', 1, 1),
(14, 4, 'string', 1, 2),
(15, 4, 'boolean', 1, 3),
(16, 4, 'document', 0, 4),
(17, 6, '5', 0, 1),
(18, 6, 'Ошибка (Notice: Undefined variable)', 1, 2),
(19, 6, 'NULL', 0, 3),
(20, 6, '0', 0, 4),
(21, 7, 'sort()', 1, 1),
(22, 7, 'asort()', 1, 2),
(23, 7, 'msort()', 0, 3),
(24, 7, 'krsort()', 1, 4),
(25, 7, 'array_sort()', 0, 5),
(26, 9, '== сравнивает значения и типы, === только значения', 0, 1),
(27, 9, '== сравнивает только значения, === сравнивает значения и типы данных', 1, 2),
(28, 9, 'Нет никакой разницы, это синонимы', 0, 3),
(29, 9, '=== используется только для объектов', 0, 4),
(30, 10, '$_GET', 1, 1),
(31, 10, '$_POST', 1, 2),
(32, 10, '$_REQUEST', 1, 3),
(33, 10, '$GLOBALS', 1, 4),
(34, 10, '$_VARIABLES', 0, 5),
(35, 12, 'start_session()', 0, 1),
(36, 12, 'session_start()', 1, 2),
(37, 12, 'session_init()', 0, 3),
(38, 12, 'ob_start()', 0, 4),
(39, 13, 'boolean', 1, 1),
(40, 13, 'integer', 1, 2),
(41, 13, 'float', 1, 3),
(42, 13, 'string', 1, 4),
(43, 13, 'array', 0, 5),
(44, 13, 'object', 0, 6),
(45, 14, 'Значения из второго массива перезапишут значения из первого массива', 0, 1),
(46, 14, 'Значения из первого массива сохранятся, а значения из второго массива с теми же ключами будут проигнорированы', 1, 2),
(47, 14, 'PHP выдаст фатальную ошибку', 0, 3),
(48, 14, 'Ключи автоматически переименуются', 0, 4);

-- --------------------------------------------------------

--
-- Структура таблицы `site_settings`
--

CREATE TABLE `site_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `site_settings`
--

INSERT INTO `site_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'site_name', 'StudTest', '2026-06-16 01:21:28'),
(2, 'site_description', 'Система онлайн-тестирования студентов', '2026-06-16 01:00:05'),
(3, 'default_theme', 'light', '2026-06-16 01:00:05'),
(4, 'allow_registration', '1', '2026-06-16 01:00:05');

-- --------------------------------------------------------

--
-- Структура таблицы `student_groups`
--

CREATE TABLE `student_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `course` int(11) NOT NULL,
  `curator_name` varchar(150) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `student_groups`
--

INSERT INTO `student_groups` (`id`, `name`, `course`, `curator_name`, `created_at`) VALUES
(1, 'ПВТ-9-22', 4, 'Бубнов Андрей Вальеревич', '2026-06-16 01:00:05'),
(2, 'ЭПП-9-22', 4, 'Марина Сергеевна', '2026-06-16 01:00:05'),
(3, 'ПОВТ-31', 3, 'Серикбаев Нурлан Ермекович', '2026-06-16 01:00:05');

-- --------------------------------------------------------

--
-- Структура таблицы `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `description`, `teacher_id`, `created_at`) VALUES
(1, 'Основы программирования', 'Проверка базовых знаний по программированию.', 2, '2026-06-16 01:00:05'),
(2, 'Базы данных', 'Проектирование и использование баз данных.', 2, '2026-06-16 01:00:05'),
(3, 'Web-разработка', 'Разработка сайтов на PHP, HTML, CSS и JavaScript.', 2, '2026-06-16 01:00:05');

-- --------------------------------------------------------

--
-- Структура таблицы `tests`
--

CREATE TABLE `tests` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `subject_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `time_limit` int(11) NOT NULL DEFAULT 30,
  `max_attempts` int(11) NOT NULL DEFAULT 1,
  `passing_score` int(11) NOT NULL DEFAULT 50,
  `shuffle_questions` tinyint(1) NOT NULL DEFAULT 1,
  `shuffle_answers` tinyint(1) NOT NULL DEFAULT 1,
  `status` enum('draft','published','closed') NOT NULL DEFAULT 'draft',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `tests`
--

INSERT INTO `tests` (`id`, `title`, `description`, `subject_id`, `teacher_id`, `time_limit`, `max_attempts`, `passing_score`, `shuffle_questions`, `shuffle_answers`, `status`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, 'Тест по основам PHP', 'Проверка знаний студентов по основам языка PHP.', 1, 2, 30, 2, 60, 1, 1, 'published', '2026-06-16 04:00:05', '2026-07-16 04:00:05', '2026-06-16 01:00:05', '2026-06-16 01:00:05');

-- --------------------------------------------------------

--
-- Структура таблицы `test_attempts`
--

CREATE TABLE `test_attempts` (
  `id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `started_at` datetime NOT NULL DEFAULT current_timestamp(),
  `finished_at` datetime DEFAULT NULL,
  `score` int(11) DEFAULT 0,
  `total_points` int(11) DEFAULT 0,
  `earned_points` int(11) DEFAULT 0,
  `status` enum('in_progress','finished','expired') NOT NULL DEFAULT 'in_progress',
  `ip_address` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `test_attempts`
--

INSERT INTO `test_attempts` (`id`, `test_id`, `student_id`, `started_at`, `finished_at`, `score`, `total_points`, `earned_points`, `status`, `ip_address`) VALUES
(1, 1, 3, '2026-06-16 04:00:55', '2026-06-16 04:00:55', 17, 6, 1, 'finished', '127.0.0.1'),
(2, 1, 3, '2026-06-16 04:39:02', '2026-06-16 04:39:02', 13, 16, 2, 'finished', '127.0.0.1');

-- --------------------------------------------------------

--
-- Структура таблицы `test_groups`
--

CREATE TABLE `test_groups` (
  `id` int(11) NOT NULL,
  `test_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `test_groups`
--

INSERT INTO `test_groups` (`id`, `test_id`, `group_id`) VALUES
(1, 1, 1),
(2, 1, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL DEFAULT 'student',
  `group_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `role`, `group_id`, `is_active`, `avatar`, `created_at`, `updated_at`) VALUES
(1, 'Администратор системы', 'admin@test.kz', '$2y$10$huIaTXaqQ/8Ma8fEDMykKOrww22cKUtaP01buJyyT83MgTxGZfdgK', 'admin', NULL, 1, NULL, '2026-06-16 01:00:05', '2026-06-16 01:00:05'),
(2, 'Бубнов Андрей Вальеревич', 'teacher@test.kz', '$2y$10$huIaTXaqQ/8Ma8fEDMykKOrww22cKUtaP01buJyyT83MgTxGZfdgK', 'teacher', NULL, 1, NULL, '2026-06-16 01:00:05', '2026-06-16 01:00:05'),
(3, 'Базарбаев Рахат Адилбекович', 'student@test.kz', '$2y$10$huIaTXaqQ/8Ma8fEDMykKOrww22cKUtaP01buJyyT83MgTxGZfdgK', 'student', 1, 1, NULL, '2026-06-16 01:00:05', '2026-06-16 01:00:05'),
(4, 'Ка1шанов Мирас', 'miras@test.kz', '$2y$10$huIaTXaqQ/8Ma8fEDMykKOrww22cKUtaP01buJyyT83MgTxGZfdgK', 'student', 3, 1, NULL, '2026-06-16 01:00:05', '2026-06-16 01:20:36'),
(5, 'Ильин Максим', 'maksim@mail.ru', '$2y$10$9lmnOtJOOb.1U6UIfIUC0OknWIm15sSBjLN4O4bfJSWxvTVfheK2a', 'student', 1, 1, NULL, '2026-06-16 01:42:07', '2026-06-16 01:42:07');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_activity_user` (`user_id`);

--
-- Индексы таблицы `attempt_answers`
--
ALTER TABLE `attempt_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_attempt_answers_attempt` (`attempt_id`),
  ADD KEY `fk_attempt_answers_question` (`question_id`),
  ADD KEY `fk_attempt_answers_option` (`selected_option_id`);

--
-- Индексы таблицы `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notifications_user` (`user_id`);

--
-- Индексы таблицы `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_questions_test` (`test_id`);

--
-- Индексы таблицы `question_imports`
--
ALTER TABLE `question_imports`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `question_options`
--
ALTER TABLE `question_options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_options_question` (`question_id`);

--
-- Индексы таблицы `site_settings`
--
ALTER TABLE `site_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Индексы таблицы `student_groups`
--
ALTER TABLE `student_groups`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_subject_teacher` (`teacher_id`);

--
-- Индексы таблицы `tests`
--
ALTER TABLE `tests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_tests_subject` (`subject_id`),
  ADD KEY `fk_tests_teacher` (`teacher_id`);

--
-- Индексы таблицы `test_attempts`
--
ALTER TABLE `test_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_attempts_test` (`test_id`),
  ADD KEY `fk_attempts_student` (`student_id`);

--
-- Индексы таблицы `test_groups`
--
ALTER TABLE `test_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_test_groups_test` (`test_id`),
  ADD KEY `fk_test_groups_group` (`group_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_users_group` (`group_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT для таблицы `attempt_answers`
--
ALTER TABLE `attempt_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT для таблицы `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `question_imports`
--
ALTER TABLE `question_imports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `question_options`
--
ALTER TABLE `question_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT для таблицы `site_settings`
--
ALTER TABLE `site_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `student_groups`
--
ALTER TABLE `student_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `tests`
--
ALTER TABLE `tests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `test_attempts`
--
ALTER TABLE `test_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `test_groups`
--
ALTER TABLE `test_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `fk_activity_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `attempt_answers`
--
ALTER TABLE `attempt_answers`
  ADD CONSTRAINT `fk_attempt_answers_attempt` FOREIGN KEY (`attempt_id`) REFERENCES `test_attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attempt_answers_option` FOREIGN KEY (`selected_option_id`) REFERENCES `question_options` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_attempt_answers_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notifications_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `fk_questions_test` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `question_options`
--
ALTER TABLE `question_options`
  ADD CONSTRAINT `fk_options_question` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `fk_subject_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Ограничения внешнего ключа таблицы `tests`
--
ALTER TABLE `tests`
  ADD CONSTRAINT `fk_tests_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_tests_teacher` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `test_attempts`
--
ALTER TABLE `test_attempts`
  ADD CONSTRAINT `fk_attempts_student` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_attempts_test` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `test_groups`
--
ALTER TABLE `test_groups`
  ADD CONSTRAINT `fk_test_groups_group` FOREIGN KEY (`group_id`) REFERENCES `student_groups` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_test_groups_test` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`) ON DELETE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_group` FOREIGN KEY (`group_id`) REFERENCES `student_groups` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
