<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace helpers;

use textFormats\jsonTextFormat;
use app;

/**
 * Description of ajaxHelper
 *
 * @author gravit
 */
class ajaxHelper
{
    //put your code here
    /**
     * @param $status
     */
    static function returnStatus($status)
    {
        echo jsonTextFormat::encode(['status' => $status]);
        app::stop();
    }

    /**
     * @param $status
     * @param $errorarray
     */
    static function returnError($status, $errorarray)
    {
        echo jsonTextFormat::encode(['status' => $status, 'error' => $errorarray]);
        app::stop();
    }

    /**
     * @param $status
     * @param $arr
     */
    static function returnData($status, $arr)
    {
        echo jsonTextFormat::encode(['status' => $status, 'content' => $arr]);
        app::stop();
    }

    /**
     * @param $formkey
     * @param $userid
     * @return string
     */
    static function newCSRFToken($formkey, $userid)
    {
        $chars = 'abcdefhiknrstyzABCDEFGHKNQRSTYZ1234567890';
        $numChars = strlen($chars);
        $string = '';
        $max = rand(4, 16);
        for ($i = 0; $i < $max; $i++) {
            $string .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        $token = $string . '.' . md5($formkey . $userid . CSRF_SECRET . $formkey . $string);
        return $token;
    }

    /**
     * @param $token
     * @param $formkey
     * @param $userid
     * @return bool
     */
    static function verifyCSRFToken($token, $formkey, $userid)
    {
        $str = explode('.', $token);
        $key = $str[0];
        if ($str[1] == md5($formkey . $userid . CSRF_SECRET . $formkey . $key)) return true;
        else return false;
    }
}
