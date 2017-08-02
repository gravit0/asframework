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
        \controllers\api\userAction::permissionsAction($args);
    }
    function flagsAction($args)
    {
        \controllers\api\userAction::flagsAction($args);
    }
    function exitAction($args)
    {
        \controllers\api\userAction::exitAction($args);
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
