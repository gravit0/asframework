<?php
require(__DIR__ . '/../config/main.php');
$cfg = include(__DIR__ . '/../config/cfg.php');
require(__DIR__ . '/../framework/abstract.php');
require(__DIR__ . '/../framework/app.php');
app::$cfg = $cfg;
app::$type = app::TYPE_WEB;
app::init();
$args = [];
if (!empty($_GET)) {
    foreach ($_GET as $n => $v) {
        $args[$n]=$v;
    }
}
if (!empty($_POST)) {
    foreach ($_POST as $n => $v) {
        $args[$n]=$v;
    }
}
app::$status = app::STATUS_LOAD;
$controllerName = null;
if(!isset($args['r'])) $controllerName = 'indexController';
else $controllerName = $args['r'].'Controller';
$file = 'controllers\\'.$controllerName;
app::$user = Account::getByToken();
try{
    app::$controller = new $file;
}
catch(сlassNotLoadedException $e)
{
    $file = 'controllers\\indexController';
    app::$controller = new $file;
}
$func = null;
if (isset($args['a']) && $args['a']) $func = $args['a'].'Action';
app::$status = app::STATUS_VERIFY;
if(app::$options & app::FLAG_CSRF_VERIFY)
{
    $userid = 0;
    if(app::$user) $userid = app::$user->id;
    $res = \helpers\ajaxHelper::verifyCSRFToken($args['csrf-token'],app::$controller->_csrf_formkey($args['a']),$userid);
    if(!$res) throw new Exception($args['csrf-token']);
}
if (!$func || !method_exists(app::$controller, $func)) {
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
