<?php
require_once __DIR__.'/config/database.php'; require_once __DIR__.'/includes/auth.php'; requireLogin(); checkCsrf();
$pageTitle='Профиль';
if($_SERVER['REQUEST_METHOD']==='POST'){
    $name=trim($_POST['full_name']??''); $email=trim($_POST['email']??'');
    if($name && $email){
        $pdo->prepare('UPDATE users SET full_name=?, email=? WHERE id=?')->execute([$name,$email,currentUserId()]);
        $_SESSION['full_name']=$name; $_SESSION['email']=$email;
        if(!empty($_POST['new_password'])) $pdo->prepare('UPDATE users SET password=? WHERE id=?')->execute([password_hash($_POST['new_password'],PASSWORD_DEFAULT),currentUserId()]);
        flash('success','Профиль обновлён'); logAction($pdo,currentUserId(),'Обновление профиля'); header('Location: /profile.php'); exit;
    }
}
$s=$pdo->prepare('SELECT u.*, g.name group_name FROM users u LEFT JOIN student_groups g ON g.id=u.group_id WHERE u.id=?'); $s->execute([currentUserId()]); $user=$s->fetch();
include __DIR__.'/includes/header.php';
?>
<div class="grid grid-2">
 <div class="card">
  <h3>👤 Личные данные</h3>
  <form class="form" method="post"><input type="hidden" name="csrf" value="<?= csrfToken() ?>">
   <div class="field"><label>ФИО</label><input name="full_name" value="<?= e($user['full_name']) ?>" required></div>
   <div class="field"><label>Email</label><input type="email" name="email" value="<?= e($user['email']) ?>" required></div>
   <div class="field"><label>Новый пароль</label><input type="password" name="new_password" placeholder="Оставьте пустым, если не менять"></div>
   <button class="btn">Сохранить изменения</button>
  </form>
 </div>
 <div class="card">
  <h3>✨ Карточка пользователя</h3>
  <div class="certificate"><div class="avatar" style="margin:0 auto 15px;width:76px;height:76px;font-size:28px"><?= e(mb_substr($user['full_name'],0,1)) ?></div><h2><?= e($user['full_name']) ?></h2><p><?= e(roleLabel($user['role'])) ?><?= $user['group_name'] ? ' · '.e($user['group_name']) : '' ?></p><span class="badge success">Активен</span></div>
 </div>
</div>

<?php include __DIR__.'/includes/footer.php'; ?>
