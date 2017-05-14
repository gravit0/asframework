#!/bin/php
<?php
error_reporting(E_ALL & ~E_NOTICE);
require(__DIR__ . '/config/main.php');
$cfg = include(__DIR__ . '/config/cfg.php');
require(__DIR__ . '/framework/abstract.php');
require(__DIR__ . '/framework/app.php');
app::$cfg = $cfg;
app::$type = app::TYPE_CONSOLE;
app::init();
app::$request->ip = inet_pton("127.0.0.1");
app::$request->versionIp = 4;
$args = [];
$key = null;
unset($argv[0]);
foreach($argv as $v)
{
    if(!strncmp($v,'-',1))
    {
        $key=substr($v,1);
    }
    else
    {
        $args[$key]=$v;
    }
}
unset($v);
app::$status = app::STATUS_LOAD;
$controllerName = null;
if(!isset($args['r'])) $controllerName = 'consoleController';
else $controllerName = $args['r'].'Controller';
$file = 'controllers\\'.$controllerName;
app::$user = Account::getById(1);
try{
    app::$controller = new $file;
}
catch(сlassNotLoadedException $e)
{
    $file = 'controllers\\apiController';
    app::$controller = new $file;
}
$func = $args['a'];
app::$status = app::STATUS_VERIFY;
if(!$func || !method_exists(app::$controller, $func))
{
    $func = 'request';
}
app::$status = app::STATUS_RUN;
$controller_result = app::$controller->$func($args);
if((app::$options & app::FLAG_VISUAL_CONTROLLER) && $controller_result)
{
    visual::render($controller_result[0],$controller_result[1]);
}
app::$status = app::STATUS_POSTRUN;
app::stop();
