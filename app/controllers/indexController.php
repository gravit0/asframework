<?php

namespace controllers;

use loggers\FileLogger;
use Controller;
use visual;
use Account;
use app;

class indexController extends Controller
{
    function __construct()
    {
        app::$options = app::$options | app::FLAG_VISUAL_CONTROLLER;
    }

    function request($args)
    {
        return $this->indexAction($args);
    }

    function indexAction($args)
    {
        $log = new FileLogger;
        $log->log('request index', 'I');
        visual::$activeid = 'index';
        return ['view', []];
    }

    function userAction($args)
    {
        $log = new FileLogger;
        $log->log('request user', 'I');
        visual::$activeid = 'index';
        if (!$args['id']) $args['id'] = 1;
        $account = Account::getById($args['id']);
        if (!$account) {
            visual::renderHttpError(404);
            app::stop();
        }
        return ['userview', ['account' => $account]];
    }

    function authAction($args)
    {
        $log = new FileLogger;
        visual::$activeid = 'index_auth';
        return ['auth', []];
    }

    function exitAction($args)
    {
        if (app::$user) {
            app::$user->close();
            setcookie('token', '');
            setcookie('tokenid', '');
        }
        visual::$activeid = 'index';
        return ['view', []];
    }

    function regAction($args)
    {
        $log = new FileLogger;
        visual::$activeid = 'index_reg';
        return ['reg', []];
    }
}
