<p>Никнейм: <?= $account->login ?></p>
<p>EMail: <?= $account->email ?></p>
<p>ID пользователя: <?= $account->id ?></p>
<p>Последний раз был: <?= $account->last_login ?></p>
Группы:
<div class="block">
<?php
function renderGroup($text)
{
    echo '<div class="user_permission">'.$text.'</div>';
}
if($account->isPermission(PERM_SUPERUSER)) renderGroup('Суперпользователь');
if($account->isPermission(PERM_READ)) renderGroup('Чтение');
if($account->isPermission(PERM_ADMIN)) renderGroup('Администратор');
if($account->isPermission(PERM_MODER)) renderGroup('Модератор');
?>
</div>
