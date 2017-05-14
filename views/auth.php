<?php

use \widgets\Form;
$f = new Form;
$f->prefix = 'testauth_';
$f->url = 'a=auth';
$f->csrf_key = 'auth';
$f->setSucsess('location.href = \'?r=index\'');
$f->addInput('login','text','Ваш логин:<br>');
$f->addInput('pass','password','Ваш пароль:<br>');
$f->addInput('test','text','Введите число:<br>','integer');
$f->addButton('butt','Авторизироватся');
$f->end();
