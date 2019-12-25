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
            return ['status' => 200];
        } catch (AccountException $e) {
            $msg = $e->getMessage();
            if ($msg == AccountException::AuthError) {
                return ['status' => 401,
                    'error' => [
                        'code' => $msg,
                        'text' => 'Login or password is incorrect'
                    ]];
            }
            if ($msg == AccountException::NoLoginError) {
                return ['status' => 401,
                    'error' => [
                        'code' => $msg,
                        'text' => 'This account is not allowed to sign in.'
                    ]];
            }
            if ($msg == AccountException::FatalBanError) {
                return ['status' => 401,
                    'error' => [
                        'code' => $msg,
                        'text' => 'You are permanently banned'
                    ]];
            }
        }
    }

    static function permissionsAction($aperm, $action, $id)
    {
        $perm = 0;
        if ($aperm == 'ADM') {
            $perm = PERM_ADMIN;
        }
        else if ($aperm == 'MODER') {
            $perm = PERM_MODER;
        }
        else if ($aperm == 'READ') {
            $perm = PERM_READ;
        }
        else if ($aperm == 'SUPERUSER') {
            if (!app::$user->isPermission(PERM_SUPERUSER))
                return ['status' => 403];
            $perm = PERM_SUPERUSER;
        } else
            return ['status' => 400];
        $account = Account::getById($id);
        if ($action == 'add') {
            $account->addPermission($perm);
        }
        else if ($action == 'rm') {
            if (!$perm)
                return ['status' => 400];
            $account->rmPermission($perm);
        } else return ['status' => 400];
        $account->pushPermissions();
        return ['status' => 200];
    }

    static function flagsAction($aperm, $action, $id)
    {
        if ($aperm == 'HIDDEN') {
            $perm = FLAG_HIDDEN;
        }
        else if ($aperm == 'SYSTEM') {
            $perm = FLAG_SYSTEM;
        }
        else if ($aperm == 'NOLOGIN') {
            $perm = FLAG_NOLOGIN;
        }
        else if ($aperm == 'FATALBAN') {
            $perm = FLAG_FATALBAN;
        } else
            return ['status' => 400];
        $account = Account::getById($id);
        if ($action == 'add') {
            $account->addFlag($perm);
        }
        if ($action == 'rm') {
            if (!$perm)
                return ['status' => 400];
            $account->rmFlag($perm);
        } else return ['status' => 400];
        $account->pushFlags();
        return ['status' => 200];
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
                return ['status' => 404];
        } else
            $acc = Account::getById($id);
        $results['id'] = $acc->id;
        $results['login'] = $acc->login;
        $results['permisions'] = $acc->permissions;
        $results['flags'] = $acc->flags;
        $results['isAuth'] = $isAuth;
        return ['status' => 200,
            'user' => $results];
    }

    static function exitAction()
    {
        app::$user->close();
        setcookie("auth_token", '', 0);
        setcookie("auth_tokenid", '', 0);
    }

}
