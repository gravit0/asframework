<?php
require(__DIR__ . '/../config/main.php');
require(__DIR__ . '/../framework/abstract.php');
require(__DIR__ . '/../framework/app.php');
$cfg = include(__DIR__ . '/../config/cfg.php');
app::$cfg = $cfg;
app::$type = app::TYPE_WEB;
app::init();
$args = [];
if (!empty($_GET)) {
    foreach ($_GET as $n => $v) {
        $args[$n] = $v;
    }
}
if (!empty($_POST)) {
    foreach ($_POST as $n => $v) {
        $args[$n] = $v;
    }
}
app::$status = app::STATUS_LOAD;
$controllerName = null;

if (!isset($args['r'])) $controllerName = 'indexController';
else {
    //Check Security
    if (!(strpos($args['r'], '\\') === false)) throw new SecurityException('bad controller ' . $args['r'] . 'Controller');
    if (!(strpos($args['r'], '/') === false)) throw new SecurityException('bad controller ' . $args['r'] . 'Controller');
    //----
    $controllerName = $args['r'] . 'Controller';
}
$file = 'controllers\\' . $controllerName;
app::loadModule("Account");
if (!$args['access_token'])
    app::$user = Account::getByToken();
else
    app::$user = Account::getByAccessToken($args['access_token']);
try {
    app::$controller = new $file;
} catch (сlassNotLoadedException $e) {
    if (isset($args['r'])) {
        visual::renderHttpError(404);
        app::stop();
    }
    $file = 'controllers\\indexController';
    app::$controller = new $file;
}
$func = null;
if (isset($args['a']) && $args['a']) $func = $args['a'] . 'Action';
if ((app::$options & app::FLAG_VISUAL_CONTROLLER)) {
    app::loadModule("visual");
}
app::$status = app::STATUS_VERIFY;
app::verify($args);
if (!$func || !method_exists(app::$controller, $func)) {
    $func = 'request';
}
app::$status = app::STATUS_RUN;
$controller_result = app::$controller->$func($args);
if ((app::$options & app::FLAG_VISUAL_CONTROLLER) && $controller_result) {
    visual::render($controller_result[0], $controller_result[1]);
}
app::$status = app::STATUS_POSTRUN;
app::stop();
