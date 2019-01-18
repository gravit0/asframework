<?php

namespace widgets;

use app;
use helpers\ajaxHelper;

/**
 * Class Form
 * @package widgets
 */
class Form
{
    /**
     * @var string
     */
    public $html;
    /**
     * @var array
     */
    public $ids;
    /**
     * @var string
     */
    public $url;
    /**
     * @var string
     */
    public $jsfunc;
    /**
     * @var string
     */
    public $buttonid;
    /**
     * @var string
     */
    public $prefix;
    /**
     * @var string
     */
    public $csrf_key;

    /**
     * @param $id
     * @param $type
     * @param $description
     * @param null $verifyrule
     * @param string $cssclass
     */
    function addInput($id, $type, $description, $verifyrule = null, $cssclass = "textinput")
    {
        $this->ids[] = ['rule' => $verifyrule, 'id' => $id];
        $this->html .= $description . '<input id="' . $this->prefix . $id . '" type="' . $type . '"';
        if ($cssclass) $this->html .= ' class="' . $cssclass . '"';
        $this->html .= '><br>';
    }

    /**
     * @param $jsdata
     */
    function setSucsess($jsdata)
    {
        $this->jsfunc = $jsdata;
    }

    /**
     * @param $id
     * @param $value
     * @param string $cssclass
     */
    function addButton($id, $value, $cssclass = "button button_blue")
    {
        $this->buttonid = $id;
        $this->html .= '<input id="' . $this->prefix . $id . '" type="submit" value="' . $value . '"';
        if ($cssclass) $this->html .= ' class="' . $cssclass . '"';
        $this->html .= '><br>';
    }

    /**
     *
     */
    function end()
    {
        echo $this->html;
        echo '<script>';
        $userid = 0;
        if (app::$user) $userid = app::$user->id;
        $cfrf_key = $this->csrf_key;
        if (!$cfrf_key) $cfrf_key = $this->prefix;
        $urlpath = $this->url . '&csrf-token=' . ajaxHelper::newCSRFToken($this->csrf_key, $userid);
        echo 'var ' . $this->prefix . $this->buttonid . ' = $("#' . $this->prefix . $this->buttonid . '");';
        echo "\n";

        foreach ($this->ids as $v) {
            echo 'var ' . $this->prefix . $v['id'] . ' = $("#' . $this->prefix . $v['id'] . '");';
            $urlpath .= '&' . $v['id'] . '="+' . $this->prefix . $v['id'] . '[0].value+"';
            echo "\n";
            if ($v['rule']) {
                echo $this->prefix . $v['id'] . '.on(\'focusout\',function() {';
                echo "\n";
                if ($v['rule'] == 'integer') {
                    echo 'if(isNaN(' . $this->prefix . $v['id'] . '[0].value))';
                }
                echo $this->prefix . $v['id'] . '.css(\'background-color\',\'red\');';
                echo "\n";
                echo ' else ';
                echo "\n";
                echo $this->prefix . $v['id'] . '.css(\'background-color\',\'\');';
                echo "\n";
                echo '});';
            }
        }
        echo $this->prefix . $this->buttonid . '.on(\'click\',function() {';
        echo "\n";
        echo "\n";
        echo 'apirequest("' . $urlpath . '",function(data)
        {
            ' . $this->jsfunc . '
        });';
        echo "\n";
        echo '});';

        echo '</script>';
    }
}
