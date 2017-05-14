<?php
namespace controllers;
use loggers\FileLogger;
use Controller;
use visual;
use Account;
use app;
class indexController  extends Controller
{
    function __construct()
    {
        app::$options = app::$options | app::FLAG_VISUAL_CONTROLLER;
    }
    function request($args)
    {
        return $this->index($args);
    }
    function index($args)
    {
        $log = new FileLogger;
        $log->log('request index','I');
        visual::$activeid='index';
        return ['view',[]];
    }
    function user($args)
    {
        $log = new FileLogger;
        $log->log('request user','I');
        visual::$activeid='index';
        if(!$args['id']) $args['id'] = 1;
        $account = Account::getById($args['id']);
        if(!$account)
        {
            visual::renderHttpError(404);
            app::stop();
        }
        return ['userview',['account'=>$account]];
    }
    function auth($args)
    {
        $log = new FileLogger;
        visual::$activeid='index_auth';
        return ['auth',[]];
    }
    function exit($args)
    {
        if(app::$user) {
            app::$user->close();
            setcookie('token','');
            setcookie('tokenid','');
        }
        visual::$activeid='index';
        return ['view',[]];
    }
    function reg($args)
    {
        $log = new FileLogger;
        visual::$activeid='index_reg';
        return ['reg',[]];
    }
}
