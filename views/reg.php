<?php

use \widgets\Form;
$f = new Form;
$f->prefix = 'testreg_';
$f->url = 'a=reg';
$f->setSucsess('location.href = \'?r=index\'');
$f->addInput('login','text','Логин:<br>');
$f->addInput('pass','password','Пароль:<br>');
$f->addInput('test','text','EMail:<br>');
$f->addButton('butt','Зарегистрироватся');
$f->end();
