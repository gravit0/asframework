<?php
namespace controllers;
use visual;
use Controller;
use db\PDOConnect;
use PDO;
use app;
use Account;
use EventManager;
class testController  extends Controller
{
    function request($args)
    {
        //if(!app::$db) app::$db = new PDOConnect;
        //$results = app::$db->prepare('SELECT * FROM users');
        //$results->execute();
        //$acc->setPassword('123');
        //$acc = new Account;
        //$acc->auth('root','123');
        visual::$activeid='test';
        if(!app::$user)
        {
            visual::renderHttpError(403);
            app::stop();
        }
        EventManager::sendEvent(1,2,1,0,'DATA!');
        visual::render('userview', ['account'=>app::$user]);
        //$results = $results->fetchAll(PDO::FETCH_ASSOC);
        //visual::render('view',['testkey'=>json_encode($acc->arr)]);
    }
}
