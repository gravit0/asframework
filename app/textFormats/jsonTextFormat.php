<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace textFormats;

/**
 * Description of jsonTextFormat
 *
 * @author gravit
 */
class jsonTextFormat
{
    //put your code here
    /**
     * @param $data
     * @return false|string
     */
    static function encode($data)
    {
        return json_encode($data);
    }

    /**
     * @param $data
     * @return mixed
     */
    static function decode($data)
    {
        return json_decode($data, true);
    }
}
