<?php

class Request
{

    public $post;
    public $get;
    public $ip;
    public $versionIp;

}

class app
{
    /**
     * @var Controller
     */
    static $controller;
    /**
     * @var AbstractTest
     */
    static $test;
    /**
     * @var Request
     */
    static $request;
    static $options;
    /**
     * @var array
     */
    static $cfg;
    /**
     * @var \db\PDOConnect
     */
    static $db;
    /**
     * @var Account
     */
    static $user;
    /**
     * @var \loggers\FileLogger
     */
    static $logger;
    static $type;
    static $status;

    const TYPE_WEB = 1;
    const TYPE_TEST = 2;
    const TYPE_CONSOLE = 3;
    const TYPE_NONE = 4;
    const STATUS_PRELOAD = 1;
    const STATUS_LOAD = 2;
    const STATUS_USR1 = 3;
    const STATUS_VERIFY = 4;
    const STATUS_USR2 = 5;
    const STATUS_RUN = 6;
    const STATUS_RENDER = 7;
    const STATUS_POSTRUN = 8;
    const STATUS_STOP = 9;
    const FLAG_NOCSRF_VERIFY = 1;
    const FLAG_VISUAL_CONTROLLER = 2;

    /**
     * @param $e Exception
     */
    static function exception_handler($e)
    {
        if (!($e instanceof NoLoggableException)) {
            $class = cfg_class_logger;
            app::$logger = new $class;
            app::$logger->err(['IP' => inet_ntop(app::$request->ip), 'IPv' => ((string)app::$request->versionIp), 'class' => get_class($e), 'message' => $e->getMessage(), 'Trace:' => $e->getTrace()], 'Exception');
        }
        if (app::$type == app::TYPE_CONSOLE) {
            echo "Exception!\n";
            echo 'Class ' . get_class($e) . "\n";
            echo 'Message ' . $e->getMessage() . "\n";
            echo 'Trace ' . var_dump($e->getTrace()) . "\n";
        } else if (!app::$type || app::$type == app::TYPE_WEB) {
            if (DEBUG_MODE) {
                try {
                    visual::renderView("exceptions/debug", ["e" => $e]);
                } catch (Error $e) {
                    echo 'Exception!<br>';
                    echo 'Class ' . get_class($e) . '<br>';
                    echo 'Message ' . htmlspecialchars($e->getMessage()) . '<br>';
                    echo 'Trace ' . json_encode($e->getTrace()) . '<br>';
                } catch (Exception $ex) {
                    echo 'Exception!<br>';
                    echo 'Class ' . get_class($e) . '<br>';
                    echo 'Message ' . htmlspecialchars($e->getMessage()) . '<br>';
                    echo 'Trace ' . json_encode($e->getTrace()) . '<br>';
                }
            } else {
                try {
                    visual::renderView("exceptions/production");
                } catch (Error $e) {
                    echo 'Произошла серьезная ошибка при обработке запроса.<br>';
                    echo 'Свяжитесь с администратором для выяснения проблемы<br>';
                    echo 'Если проблема имеет массовый характер, мы о ней уже знаем<br>';
                } catch (Exception $ex) {
                    echo 'Произошла серьезная ошибка при обработке запроса.<br>';
                    echo 'Свяжитесь с администратором для выяснения проблемы<br>';
                    echo 'Если проблема имеет массовый характер, мы о ней уже знаем<br>';
                }
            }
        }
    }

    static function stop()
    {
        app::$status = app::STATUS_STOP;
        if (!app::$type || app::$type == app::TYPE_WEB)
            exit();
        if (app::$type == app::TYPE_TEST)
            app::$test->stop();
    }

    static function load($class_name)
    {
        $class = DirSite . DirAppData . str_replace('\\', '/', $class_name) . '.php';
        if (!file_exists($class))
            throw new classNotLoadedException($class_name);
        include $class;
    }

    static function loadModule($module_name)
    {
        $class = DirSite . DirFrameworkModules . $module_name . '.php';
        if (!file_exists($class))
            throw new classNotLoadedException($module_name);
        include $class;
    }

    static function includePHPFile($__file, $vars = null)
    {
        if (!$vars)
            $vars = [];
        extract($vars, EXTR_OVERWRITE);
        if (!file_exists($__file))
            throw new FileNotFoundException($__file);
        include $__file;
    }

    static function init()
    {
        app::$status = app::STATUS_PRELOAD;
        app::$request = new Request;
        if (app::$type != app::TYPE_CONSOLE) {
            app::$request->ip = inet_pton($_SERVER['REMOTE_ADDR']);
            if (strpos(app::$request->ip, ':'))
                app::$request->versionIp = 6;
            else
                app::$request->versionIp = 4;
        }
        spl_autoload_register(array('app', 'load'));
        set_exception_handler(array('app', 'exception_handler'));
    }

    static function verify($args)
    {
        if (app::$controller->flags & Controller::CFLAG_VERIFY_CSRF && !(app::$options & app::FLAG_NOCSRF_VERIFY)) {
            $userid = 0;
            if (app::$user)
                $userid = app::$user->id;
            $res = \helpers\ajaxHelper::verifyCSRFToken($args['csrf-token'], app::$controller->_csrf_formkey($args['a']), $userid);
            if (!$res)
                throw new BadRequestException('bad csrf token: ' . $args['csrf-token']);
        }
        if (app::$controller->flags & Controller::CFLAG_CONSOLEONLY && app::$type != app::TYPE_CONSOLE) {
            visual::renderHTTPError(403);
            app::stop();
        }
        if (app::$controller->flags & Controller:: CFLAG_VISIBLEONLY && app::$type != app::TYPE_WEB) {
            visual::renderHTTPError(403);
            app::stop();
        }
    }
}
