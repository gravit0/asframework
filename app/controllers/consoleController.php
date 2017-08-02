<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace controllers;
use helpers\consoleHelper;
use textFormats\jsonTextFormat;
use Controller;
use app;
use Account;
use visual;
/**
 * Description of apiController
 *
 * @author gravit
 */
function consoleHelpCreate($cmd,$value)
{
    echo consoleHelper::setColor(consoleHelper::TEXTCOLOR_YELLOW);
    echo $cmd;
    echo consoleHelper::setColor(consoleHelper::TEXTCOLOR_CLEAR);
    echo ' - ';
    echo $value;
    echo "\n";
}
class consoleController extends Controller {
    //put your code here
    function __construct()
    {
        if(app::$type == app::TYPE_WEB) {
            visual::renderHTTPError(403);
            app::stop();
        }
    }
    function request($args)
    {
        echo consoleHelper::textColor("Справка\n",consoleHelper::TEXTCOLOR_GREEN);
        consoleHelpCreate('-a setpassword -id [ID] -pass [password]','Смена пароля пользователя');
        consoleHelpCreate('-a repairadmin','Восстановить привилегии суперпользователя ID 1');
        consoleHelpCreate('-a getuser [ID]','Информация о пользователе');
        consoleHelpCreate('-r api -a flags -f add -id [ID] -flag [FLAG] -id [ID]','Добавить флаг пользователю');
        consoleHelpCreate('-r api -a flags -f rm -id [ID] -flag [FLAG]','Удалить флаг у пользователя');
        consoleHelpCreate('-r api -a permissions -f add -id [ID] -perm [PERMISSION]','Добавить привилегию пользователю');
        consoleHelpCreate('-r api -a permissions -f rm -id [ID] -perm [PERMISSION]','Удалить привилегию у пользователя');
    }
    function setpasswordAction($args)
    {
        if(!$args['id']) consoleHelper::textColor("ID не задан\n",consoleHelper::TEXTCOLOR_RED);
        if(!$args['pass']) consoleHelper::textColor("Новый пароль не задан\n",consoleHelper::TEXTCOLOR_RED);
        $acc = Account::getByID($args['id']);
        $acc->setPassword($args['pass']);
        echo consoleHelper::textColor("Новый пароль:",consoleHelper::TEXTCOLOR_GREEN);
        echo consoleHelper::textColor($args['pass'],consoleHelper::TEXTCOLOR_YELLOW);
        echo "\n";
    }
    function getuserAction($args)
    {
        if(!$args['id']) consoleHelper::textColor("ID не задан\n",consoleHelper::TEXTCOLOR_RED);
        $acc = Account::getByID($args['id']);
        if($acc->isFlag(FLAG_HIDDEN))
        {
            echo consoleHelper::textColor("(Скрытый пользователь)\n",consoleHelper::ATTR_FLASH.';'.consoleHelper::TEXTCOLOR_YELLOW);
        }
        if($acc->isFlag(FLAG_SYSTEM))
        {
            echo consoleHelper::textColor("(Системный пользователь)\n",consoleHelper::ATTR_FLASH.';'.consoleHelper::TEXTCOLOR_RED);
        }
        echo consoleHelper::textColor("ID: ",consoleHelper::TEXTCOLOR_YELLOW);
        echo $acc->id."\n";
        echo consoleHelper::textColor("Имя: ",consoleHelper::TEXTCOLOR_YELLOW);
        echo $acc->login."\n";
        echo consoleHelper::textColor("Email: ",consoleHelper::TEXTCOLOR_YELLOW);
        echo $acc->email."\n";
        echo consoleHelper::textColor("Last login: ",consoleHelper::TEXTCOLOR_YELLOW);
        echo $acc->last_login."\n";
        echo consoleHelper::textColor("Groups: ",consoleHelper::TEXTCOLOR_YELLOW);
        echo json_encode($acc->getAllPermissions());
        echo "\n";
    }
    function repairadminAction($args)
    {
        $flagIsPush = false;
        echo "PERM_SUPERUSER:";
        if(app::$user->isPermission(PERM_SUPERUSER))
        {
            echo consoleHelper::textColor("OK\n",consoleHelper::TEXTCOLOR_GREEN);
        }
        else
        {
            echo consoleHelper::textColor("FAIL\n",consoleHelper::TEXTCOLOR_RED);
            app::$user->addPermission(PERM_SUPERUSER);
            echo consoleHelper::textColor("SUPERUSER REPAIR OK\n",consoleHelper::TEXTCOLOR_GREEN);
            $flagIsPush = true;
        }
        echo "PERM_ADM:";
        if(app::$user->isPermission(PERM_ADMIN))
        {
            echo consoleHelper::textColor("OK\n",consoleHelper::TEXTCOLOR_GREEN);
        }
        else
        {
            echo consoleHelper::textColor("FAIL\n",consoleHelper::TEXTCOLOR_RED);
            app::$user->addPermission(PERM_ADMIN);
            echo consoleHelper::textColor("ADMIN REPAIR OK\n",consoleHelper::TEXTCOLOR_GREEN);
            $flagIsPush = true;
        }
        if($flagIsPush)
        {
            app::$user->pushPermissions();
            echo consoleHelper::textColor("PUSH PERMISSIONS OK\n",consoleHelper::TEXTCOLOR_GREEN);
        }
    }
}
