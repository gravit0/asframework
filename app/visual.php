<?php
class visual
{
    private static $viewName;
    private static $viewArgs;
    public static $head;
    public static $title;
    public static $activeid;
    static function renderView($__name,$args=[])
    {
        //if(!$args) $args = [];
        //extract($args, EXTR_OVERWRITE);
        //include DirSite . 'views/' . $__name . '.php';
        app::includePHPFile(DirSite . 'views/' . $__name . '.php',$args);
    }
    static function render($name, $args = []) {
        app::$status = app::STATUS_RENDER;
        visual::$viewName = $name;
        visual::$viewArgs = $args;
        visual::renderView('main');
    }
    static function renderBody() {
        visual::renderView(visual::$viewName, visual::$viewArgs);
    }
    static function renderHttpError($err) {
        if($err == 404)
        {
            header('HTTP/1.0 404 Not Found');
            header('Status: 404 Not Found');
        }
        visual::render('error/'.$err);
    }
    static function renderHead() {
        echo '<link type="text/css" rel="stylesheet" href="./'. DirCSS . 'index.css">';
        if(visual::$title)
        {
            echo '<title>'.visual::$title.'</title>';
        }
        echo '<script src="./'. DirJS . 'index.js"></script>';
        if(DebugMode) 
            echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>';
        else
            echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.js"></script>';
        if(visual::$head)
        echo visual::$head;
    }
}
