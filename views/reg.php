<?php

use \widgets\Form;
$f = new Form;
$f->prefix = 'testreg_';
$f->url = 'a=reg';
$f->csrf_key = 'reg';
//$f->setSucsess('location.href = \'?r=index\'');
$f->setSucsess('');
$f->addInput('login','text','Логин:<br>');
$f->addInput('pass','password','Пароль:<br>');
$f->addInput('email','text','EMail:<br>');
$f->addButton('butt','Зарегистрироватся');
$f->end();
?>
<p>Если все данные введены верно, после регистрации Вы можете авторизироватся</p>
