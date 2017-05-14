<?php
abstract class AbstractTest
{
    public $name;
    public $manager;
    public $category;
    public $stopFlag;
    abstract function init();
    abstract function body();
    abstract function stop();
    abstract function end();
}
