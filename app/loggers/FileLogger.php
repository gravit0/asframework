<?php
namespace loggers;
class FileLogger
{
    public $file;
    function __construct()
    {
        $this->file = fopen(DirSite . 'runtime/' . 'server.log','a');
    }
    function __destruct()
    {
        fclose($this->file);
    }
    function log($text,$rate=null,$category=null)
    {
        $result = date('m.d.y H:i:s') . ' ';
        if($rate) $result .= '{'.$rate.'}';
        if($category) $result .= "[$category]";
        if(is_array($text)) $result .= json_encode($text);
        else $result .= $text;
        $result .= "\n";
        fwrite($this->file,$result);
    }
    function err($text,$category=null)
    {
        $result = date('m.d.y H:i:s') . ' {E}';
        if($category) $result .= "[$category]";
        if(is_array($text)) $result .= json_encode($text);
        else $result .= $text;
        $result .= "\n";
        fwrite($this->file,$result);
    }
}
