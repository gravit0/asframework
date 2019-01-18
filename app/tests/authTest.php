<?php

namespace tests;

use AbstractTest;

class authTest extends AbstractTest
{
    function init()
    {
        $this->name = 'auth';
        $this->category = 'user';
    }

    function body()
    {
        $controller = new \controllers\apiController;
        $controller->request(['r' => 'api', 'a' => 'auth', 'login' => 'root', 'pass' => '123']);
        return ['status' => '200'];
    }

    function stop()
    {
        $this->stopFlag = true;

    }

    function end()
    {

    }
}
