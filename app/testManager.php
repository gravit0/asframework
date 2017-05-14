<?php
class TestManager
{
    function initTestApp()
    {
        app::$type=app::TYPE_TEST;
    }
    function stopTestApp()
    {
        app::$type=app::TYPE_WEB;
    }
    function startTest($classname)
    {
        $test = new $classname;
        $test->stopFlag=false;
        $test->manager=$this;
        $test->init();
        app::$test = $test;
        $test->body();
        $test->end();
    }
    function appDump()
    {
        $result = [];
        $result['controller_class'] = get_class(app::$controller);
        $result['user_class'] = get_class(app::$user);
        $result['user'] = [
            'login' => app::$user->login,
            'tokenid' => app::$user->tokenid,
            'permissions' => app::$user->permissions,
            'flags' => app::$user->flags,
            ];
        return $result;
    }
}
