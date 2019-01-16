<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace helpers;

/**
 * Description of SecurityHelper
 *
 * @author gravit
 */
class SecurityHelper
{
    //put your code here
    static function isOnlyLetter($str)
    {
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $c = $str[$i];
            $res = false;
            if ($c > 'A' && $c < 'Z') $res = true;
            if ($c > 'a' && $c < 'z') $res = true;
            if (!$res) return false;
        }
    }

    static function isAllowLetter($str)
    {
        $len = strlen($str);
        for ($i = 0; $i < $len; $i++) {
            $c = $str[$i];
            $res = false;
            if ($c > 'A' && $c < 'Z') $res = true;
            if ($c > 'a' && $c < 'z') $res = true;
            if ($c > '0' && $c < '9') $res = true;
            if ($c == ' ' || $c == '-' || $c == '_') $res = true;
            if (!$res) return false;
        }
    }
}
