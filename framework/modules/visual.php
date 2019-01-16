<?php
class visual
{
    private static $viewName;
    private static $viewArgs;
    public static $layout;
    public static $flags;
    public static $head;
    public static $title;
    public static $activeid;
    private static $isAlready = false;
    
    const FLAG_NO_STD_CSS = 1;
    const FLAG_NO_STD_JS = 2;
    const FLAG_NO_LAYOUT = 4;
    static function renderView($__name,$args=[])
    {
        //if(!$args) $args = [];
        //extract($args, EXTR_OVERWRITE);
        //include DirSite . 'views/' . $__name . '.php';
        app::includePHPFile(DirSite . 'views/' . $__name . '.php',$args);
    }
    static function render($name, $args = []) {
        if(visual::$isAlready) throw new visualException(visualException::ALREADY_TAKEN_PLACE);
        app::$status = app::STATUS_RENDER;
        visual::$viewName = $name;
        visual::$viewArgs = $args;
        if(!(visual::$flags & visual::FLAG_NO_LAYOUT)) {
            $layout = visual::$layout;
            if(!$layout) $layout = app::$cfg['visual']['stdLayout'];
            visual::renderView('layouts/'.$layout);
        }
        else
        {
            visual::renderView(visual::$viewName, visual::$viewArgs);
        }
        visual::$isAlready = true;
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
        if(!(visual::$flags & visual::FLAG_NO_STD_CSS)) {
            echo '<link type="text/css" rel="stylesheet" href="./'. DirCSS . app::$cfg['visual']['stdCSS'] .'">';
        }
        if(visual::$title)
        {
            echo '<title>'.visual::$title.'</title>';
        }
        if(!(visual::$flags & visual::FLAG_NO_STD_JS)) {
            echo '<script src="./'. DirJS . app::$cfg['visual']['stdJavaScript'] . '"></script>';
            if(DEBUG_MODE) 
                echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>';
            else
                echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.js"></script>';
        }
        if(visual::$head)
        echo visual::$head;
    }
}
class visualException extends appException {
    //put your code here
    const ALREADY_TAKEN_PLACE = 0;
}
