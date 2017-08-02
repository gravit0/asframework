<?php

namespace controllers\api;

use Account;
use textFormats\jsonTextFormat;
use helpers\ajaxHelper;
use Action;
use app;

class userAction extends Action {

    function authAction($args) {
        $login = $args['login'];
        $pass = $args['pass'];
        if (!$login || !$pass || app::$user)
            ajaxHelper::returnStatus(400);
        try {
            app::$user = new Account;
            app::$user->auth($login, $pass);
            setcookie("auth_token", app::$user->token, time() + 30 * 24 * 3600);
            setcookie("auth_tokenid", app::$user->tokenid, time() + 30 * 24 * 3600);
            ajaxHelper::returnStatus(200);
        } catch (AccountException $e) {
            $msg = $e->getMessage();
            if($msg == AccountException::AuthError)
            {
                echo jsonTextFormat::encode(['status' => 401,
                    'error' => [
                        'code' => $msg,
                        'text' => 'Login or password is incorrect'
                ]]);
                app::stop();
            }
            if($msg == AccountException::NoLoginError)
            {
                echo jsonTextFormat::encode(['status' => 401,
                    'error' => [
                        'code' => $msg,
                        'text' => 'This account is not allowed to sign in.'
                ]]);
                app::stop();
            }
            if($msg == AccountException::FatalBanError)
            {
                echo jsonTextFormat::encode(['status' => 401,
                    'error' => [
                        'code' => $msg,
                        'text' => 'You are permanently banned'
                ]]);
                app::stop();
            }
        }
    }
    
    function permissionsAction($args) {
        if(!$args['f']) ajaxHelper::returnStatus(400);
        if(!$args['id']) ajaxHelper::returnStatus(400);
        if(!app::$user->isPermission(PERM_ADMIN) && !app::$user->isPermission(PERM_SUPERUSER)) ajaxHelper::returnStatus(403);
        if($args['f'] == 'add')
        {
            if(!$args['perm']) ajaxHelper::returnStatus(400);
            $account = Account::getById($args['id']);
            if($args['perm'] == 'ADM')
            {
                if(!app::$user->isPermission(PERM_SUPERUSER)) ajaxHelper::returnStatus(403);
                $account->addPermission(PERM_ADMIN);
                $account->pushPermissions();
                ajaxHelper::returnStatus(200);
            }
            if($args['perm'] == 'MODER')
            {
                $account->addPermission(PERM_MODER);
                $account->pushPermissions();
                ajaxHelper::returnStatus(200);
            }
            if($args['perm'] == 'READ')
            {
                $account->addPermission(PERM_READ);
                $account->pushPermissions();
                ajaxHelper::returnStatus(200);
            }
            if($args['perm'] == 'SUPERUSER')
            {
                if(!app::$user->isPermission(PERM_SUPERUSER)) ajaxHelper::returnStatus(403);
                $account->addPermission(PERM_SUPERUSER);
                $account->pushPermissions();
                ajaxHelper::returnStatus(200);
            }
            ajaxHelper::returnStatus(400);
        }
        if($args['f'] == 'rm')
        {
            if(!$args['perm']) ajaxHelper::returnStatus(400);
            $account = Account::getById($args['id']);
            if($args['perm'] == 'ADM')
            {
                if(!app::$user->isPermission(PERM_SUPERUSER)) ajaxHelper::returnStatus(403);
                $account->rmPermission(PERM_ADMIN);
                $account->pushPermissions();
                ajaxHelper::returnStatus(200);
            }
            if($args['perm'] == 'MODER')
            {
                $account->rmPermission(PERM_MODER);
                $account->pushPermissions();
                ajaxHelper::returnStatus(200);
            }
            if($args['perm'] == 'READ')
            {
                $account->rmPermission(PERM_READ);
                $account->pushPermissions();
                ajaxHelper::returnStatus(200);
            }
            if($args['perm'] == 'SUPERUSER')
            {
                if(!app::$user->isPermission(PERM_SUPERUSER)) ajaxHelper::returnStatus(403);
                $account->rmPermission(PERM_SUPERUSER);
                $account->pushPermissions();
                ajaxHelper::returnStatus(200);
            }
            ajaxHelper::returnStatus(400);
        }
        
        ajaxHelper::returnStatus(400);
    }
    function flagsAction($args) {
        if(!$args['f']) ajaxHelper::returnStatus(400);
        if(!$args['id']) ajaxHelper::returnStatus(400);
        if(!app::$user->isPermission(PERM_SUPERUSER)) ajaxHelper::returnStatus(403);
        if($args['f'] == 'add')
        {
            if(!$args['flag']) ajaxHelper::returnStatus(400);
            $account = Account::getById($args['id']);
            if($args['flag'] == 'HIDDEN')
            {
                $account->addFlag(FLAG_HIDDEN);
                $account->pushFlags();
                ajaxHelper::returnStatus(200);
            }
            if($args['flag'] == 'SYSTEM')
            {
                $account->addFlag(FLAG_SYSTEM);
                $account->pushFlags();
                ajaxHelper::returnStatus(200);
            }
            if($args['flag'] == 'NOLOGIN')
            {
                $account->addFlag(FLAG_NOLOGIN);
                $account->pushFlags();
                ajaxHelper::returnStatus(200);
            }
            if($args['flag'] == 'FATALBAN')
            {
                $account->addFlag(FLAG_FATALBAN);
                $account->pushFlags();
                ajaxHelper::returnStatus(200);
            }
            ajaxHelper::returnStatus(400);
        }
        if($args['f'] == 'rm')
        {
            if(!$args['flag']) ajaxHelper::returnStatus(400);
            $account = Account::getById($args['id']);
            if($args['flag'] == 'HIDDEN')
            {
                $account->rmFlag(FLAG_HIDDEN);
                $account->pushFlags();
                ajaxHelper::returnStatus(200);
            }
            if($args['flag'] == 'SYSTEM')
            {
                $account->rmFlag(FLAG_SYSTEM);
                $account->pushFlags();
                ajaxHelper::returnStatus(200);
            }
            if($args['flag'] == 'NOLOGIN')
            {
                $account->rmFlag(FLAG_NOLOGIN);
                $account->pushFlags();
                ajaxHelper::returnStatus(200);
            }
            if($args['flag'] == 'FATALBAN')
            {
                $account->rmFlag(FLAG_FATALBAN);
                $account->pushFlags();
                ajaxHelper::returnStatus(200);
            }
            ajaxHelper::returnStatus(400);
        }
        
        ajaxHelper::returnStatus(400);
    }
    function getuserAction($args)
    {
        $results = [];
        $acc = null;
        $isAuth = false;
        if(!$args['id'])
        {
            if(app::$user){
                $acc = app::$user;
                $isAuth = true;
            }
            else ajaxHelper::returnStatus(400);
        }
        else $acc = Account::getById($args['id']);
        $results['id'] = $acc->id;
        $results['login'] = $acc->login;
        $results['permisions'] = $acc->permissions;
        $results['flags'] = $acc->flags;
        $results['isAuth'] = $isAuth;
        echo jsonTextFormat::encode(['status' => 200,
                    'user' => $results]);
        app::stop();
    }
    function exitAction($args) {
        if (!app::$user)
            ajaxHelper::returnStatus(400);
        app::$user->close();
        setcookie("auth_token", '', 0);
        setcookie("auth_tokenid", '', 0);
    }

}
