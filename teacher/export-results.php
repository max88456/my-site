<?php
require_once __DIR__.'/../config/database.php'; require_once __DIR__.'/../includes/auth.php'; requireRole(['teacher','admin']);
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=studtest_results.csv');
$out=fopen('php://output','w');
fputcsv($out,['Студент','Тест','Балл','Статус','Начало','Завершение'],';');
if(currentUserRole()==='teacher'){$s=$pdo->prepare('SELECT u.full_name,t.title,a.score,a.status,a.started_at,a.finished_at FROM test_attempts a JOIN users u ON u.id=a.student_id JOIN tests t ON t.id=a.test_id WHERE t.teacher_id=? ORDER BY a.started_at DESC');$s->execute([currentUserId()]);}
else{$s=$pdo->query('SELECT u.full_name,t.title,a.score,a.status,a.started_at,a.finished_at FROM test_attempts a JOIN users u ON u.id=a.student_id JOIN tests t ON t.id=a.test_id ORDER BY a.started_at DESC');}
foreach($s->fetchAll() as $r){fputcsv($out,[$r['full_name'],$r['title'],$r['score'],$r['status'],$r['started_at'],$r['finished_at']],';');}
exit;
