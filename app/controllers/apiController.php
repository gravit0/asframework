<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace controllers;

use controllers\api\userAction;
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
class apiController extends Controller
{
    //put your code here
    /**
     * @var int
     */
    public $flags = Controller::CFLAG_VERIFY_CSRF;

    /**
     * apiController constructor.
     */
    function __construct()
    {
        //app::$options = app::$options | app::FLAG_CSRF_VERIFY;
    }

    /**
     * @param $args
     */
    function request($args)
    {
        return ['status' => 400];
    }

    /**
     * @param $args
     */
    function authAction($args)
    {
        $login = $args['login'];
        $pass = $args['pass'];
        if (!$login || !$pass || app::$user)
            return ['status' => 400];
        return userAction::authAction($login, $pass);
    }

    /**
     * @param $args
     */
    function regAction($args)
    {
        if (!$args['login'] || !$args['pass'] || !$args['email']) return ['status' => 400];
        $a = Account::getByLogin($args['login']);
        if (!$a) {
            $newa = new Account;
            $newa->reg($args['login'], password_hash($args['pass'], PASSWORD_DEFAULT), $args['email']);
            return ['status' => 200];
        }
        return ['status' => 401];
    }

    /**
     * @param $args
     */
    function permissionsAction($args)
    {
        if (!$args['f']) return ['status' => 400];
        if (!$args['id']) return ['status' => 400];
        if (!app::$user->isPermission(PERM_ADMIN) && !app::$user->isPermission(PERM_SUPERUSER)) return ['status' => 403];
        if (!$args['perm'])
            return ['status' => 400];
        return userAction::permissionsAction($args['perm'], $args['f'], $args['id']);
    }

    /**
     * @param $args
     */
    function flagsAction($args)
    {
        if (!$args['f']) return ['status' => 400];
        if (!$args['id']) return ['status' => 400];
        if (!app::$user->isPermission(PERM_SUPERUSER)) return ['status' => 403];
        if (!$args['flag'])
            return ['status' => 400];
        return userAction::FlagsAction($args['flag'], $args['f'], $args['id']);
    }

    /**
     * @param $args
     * @return array
     */
    function exitAction($args)
    {
        if (!app::$user)
            return ['status' => 400];
        userAction::exitAction();
    }

    /**
     * @param $args
     */
    function getuserAction($args)
    {
        return userAction::getuserAction($args);
    }

    /**
     * @param $args
     */
    function getgroupmapAction($args)
    {
        echo ['status' => 200,
            'groupmap' => app::$cfg['users']['groupmap']];
    }
}
