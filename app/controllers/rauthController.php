<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace controllers;

/**
 * Description of rauthController
 *
 * @author gravit
 */
class rauthController extends \Controller
{
    //put your code here
    public function request($args)
    {
        \helpers\ajaxHelper::returnStatus(400);
    }

    public function getAction($args)
    {
        api\userAction::getuserAction($args);
    }
}
