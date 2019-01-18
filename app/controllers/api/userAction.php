<?php

namespace controllers\api;

use Account;
use AccountException;
use textFormats\jsonTextFormat;
use helpers\ajaxHelper;
use Action;
use app;

class userAction extends Action
{

    static function authAction($login, $pass)
    {
        try {
            app::$user = new Account;
            app::$user->auth($login, $pass);
            setcookie("auth_token", app::$user->token, time() + 30 * 24 * 3600);
            setcookie("auth_tokenid", app::$user->tokenid, time() + 30 * 24 * 3600);
            ajaxHelper::returnStatus(200);
        } catch (AccountException $e) {
            $msg = $e->getMessage();
            if ($msg == AccountException::AuthError) {
                echo jsonTextFormat::encode(['status' => 401,
                    'error' => [
                        'code' => $msg,
                        'text' => 'Login or password is incorrect'
                    ]]);
                app::stop();
            }
            if ($msg == AccountException::NoLoginError) {
                echo jsonTextFormat::encode(['status' => 401,
                    'error' => [
                        'code' => $msg,
                        'text' => 'This account is not allowed to sign in.'
                    ]]);
                app::stop();
            }
            if ($msg == AccountException::FatalBanError) {
                echo jsonTextFormat::encode(['status' => 401,
                    'error' => [
                        'code' => $msg,
                        'text' => 'You are permanently banned'
                    ]]);
                app::stop();
            }
        }
    }

    static function permissionsAction($aperm, $action, $id)
    {
        $perm = 0;
        if ($aperm == 'ADM') {
            $perm = PERM_ADMIN;
        }
        if ($aperm == 'MODER') {
            $perm = PERM_MODER;
        }
        if ($aperm == 'READ') {
            $perm = PERM_READ;
        }
        if ($aperm == 'SUPERUSER') {
            if (!app::$user->isPermission(PERM_SUPERUSER))
                ajaxHelper::returnStatus(403);
            $perm = PERM_SUPERUSER;
        } else
            ajaxHelper::returnStatus(400);
        $account = Account::getById($id);
        if ($action == 'add') {
            $account->addPermission($perm);
        }
        if ($action == 'rm') {
            if (!$perm)
                ajaxHelper::returnStatus(400);
            $account->rmPermission($perm);
        } else ajaxHelper::returnStatus(400);
        $account->pushPermissions();
        ajaxHelper::returnStatus(200);
    }

    static function flagsAction($aperm, $action, $id)
    {
        $perm = 0;
        if ($aperm == 'HIDDEN') {
            $perm = FLAG_HIDDEN;
        }
        if ($aperm == 'SYSTEM') {
            $perm = FLAG_SYSTEM;
        }
        if ($aperm == 'NOLOGIN') {
            $perm = FLAG_NOLOGIN;
        }
        if ($aperm == 'FATALBAN') {
            $perm = FLAG_FATALBAN;
        } else
            ajaxHelper::returnStatus(400);
        $account = Account::getById($id);
        if ($action == 'add') {
            $account->addFlag($perm);
        }
        if ($action == 'rm') {
            if (!$perm)
                ajaxHelper::returnStatus(400);
            $account->rmFlag($perm);
        } else ajaxHelper::returnStatus(400);
        $account->pushFlags();
        ajaxHelper::returnStatus(200);
    }

    static function getuserAction($id)
    {
        $results = [];
        $acc = null;
        $isAuth = false;
        if (!$id) {
            if (app::$user) {
                $acc = app::$user;
                $isAuth = true;
            } else
                ajaxHelper::returnStatus(400);
        } else
            $acc = Account::getById($id);
        $results['id'] = $acc->id;
        $results['login'] = $acc->login;
        $results['permisions'] = $acc->permissions;
        $results['flags'] = $acc->flags;
        $results['isAuth'] = $isAuth;
        echo jsonTextFormat::encode(['status' => 200,
            'user' => $results]);
        app::stop();
    }

    static function exitAction()
    {
        app::$user->close();
        setcookie("auth_token", '', 0);
        setcookie("auth_tokenid", '', 0);
    }

}
