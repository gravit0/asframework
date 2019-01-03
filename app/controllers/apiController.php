<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace controllers;
use helpers\ajaxHelper;
use textFormats\jsonTextFormat;
use Controller;
use Account;
use app;
/**
 * Description of apiController
 *
 * @author gravit
 */
class apiController extends Controller {
    //put your code here
    function __construct()
    {
        app::$options = app::$options | app::FLAG_CSRF_VERIFY;
    }
    function request($args)
    {
        ajaxHelper::returnStatus(400);
    }
    function authAction($args)
    {
        $login = $args['login'];
        $pass = $args['pass'];
        if (!$login || !$pass || app::$user)
            ajaxHelper::returnStatus(400);
        \controllers\api\userAction::authAction($args);
    }
    function regAction($args)
    {
        if(!$args['login'] || !$args['pass'] || !$args['email']) ajaxHelper::returnStatus(400);
        $a = Account::getByLogin($args['login']);
        if(!$a)
        {
            $newa = new Account;
            $newa->reg($args['login'],password_hash($args['pass'],PASSWORD_DEFAULT),$args['email']);
            ajaxHelper::returnStatus(200);
        }
        ajaxHelper::returnStatus(401);
    }
    function permissionsAction($args)
    {
        if(!$args['f']) ajaxHelper::returnStatus(400);
        if(!$args['id']) ajaxHelper::returnStatus(400);
        if(!app::$user->isPermission(PERM_ADMIN) && !app::$user->isPermission(PERM_SUPERUSER)) ajaxHelper::returnStatus(403);
        if (!$args['perm'])
            ajaxHelper::returnStatus(400);
        \controllers\api\userAction::permissionsAction($args['perm'],$args['f'],$args['id']);
    }
    function flagsAction($args)
    {
        if(!$args['f']) ajaxHelper::returnStatus(400);
        if(!$args['id']) ajaxHelper::returnStatus(400);
        if(!app::$user->isPermission(PERM_SUPERUSER)) ajaxHelper::returnStatus(403);
        if (!$args['flag'])
            ajaxHelper::returnStatus(400);
        \controllers\api\userAction::FlagsAction($args['flag'],$args['f'],$args['id']);
    }
    function exitAction($args)
    {
        if (!app::$user)
            ajaxHelper::returnStatus(400);
        \controllers\api\userAction::exitAction();
    }
    function getuserAction($args)
    {
        \controllers\api\userAction::getuserAction($args);
    }
    function getgroupmapAction($args)
    {
        echo jsonTextFormat::encode(['status' => 200,
                    'groupmap' => app::$cfg['users']['groupmap']]);
    }
}