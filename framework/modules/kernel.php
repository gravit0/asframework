<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of kernel
 *
 * @author gravit
 */
class kernel
{
    //put your code here
    /**
     * @var string
     */
    public static $currentdir;

    /**
     *
     */
    public static function kstop()
    {
        spl_autoload_unregister(array('app', 'load'));
        set_exception_handler(null);
    }

    /**
     *
     */
    public static function kexit()
    {
        exit(0);
    }

    /**
     * @param $newdir
     */
    public static function switch_autoload_dir($newdir)
    {
        if (kernel::$currentdir === null) {
            spl_autoload_unregister(array('app', 'load'));
            spl_autoload_register(array('kernel', 'load'));
        }
        kernel::$currentdir = $newdir;
    }

    /**
     *
     */
    public static function std_autoload_dir()
    {
        if (kernel::$currentdir !== null) {
            spl_autoload_unregister(array('kernel', 'load'));
            spl_autoload_register(array('app', 'load'));
        }
        kernel::$currentdir = null;
    }

    /**
     * @param bool $stdautoloader
     */
    public static function clear_autoload($stdautoloader = true)
    {
        $funcs = spl_autoload_functions();
        if ($funcs) {
            foreach ($funcs as $v) {
                spl_autoload_unregister($v);
            }
        }
        if ($stdautoloader) spl_autoload_register(array('app', 'load'));
        kernel::$currentdir = null;
    }

    /**
     *
     */
    public static function none()
    {

    }

    /**
     * @param $path
     * @return array
     */
    public static function set_config($path)
    {
        $cfg = include($path);
        $oldcfg = app::$cfg;
        app::$cfg = $cfg;
        return $oldcfg;
    }

    /**
     * @param $arr
     */
    public static function restore_config($arr)
    {
        app::$cfg = $arr;
    }

    /**
     *
     */
    public static function recreate_db()
    {
        app::$db = new \db\PDOConnect;
    }

    /**
     * @param $class_name
     * @throws сlassNotLoadedException
     */
    static function load($class_name)
    {
        $class = kernel::$currentdir . DirAppData . str_replace('\\', '/', $class_name) . '.php';
        if (!file_exists($class))
            throw new сlassNotLoadedException($class_name);
        include $class;
    }
}
