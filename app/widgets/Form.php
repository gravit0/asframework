<?php
namespace widgets; 
use app;
use helpers\ajaxHelper;
class Form
{
    public $html;
    public $ids;
    public $url;
    public $jsfunc;
    public $buttonid;
    public $prefix;
    public $csrf_key;
    function addInput($id,$type,$description,$verifyrule=null,$cssclass=null)
    {
        $this->ids[] = ['rule'=>$verifyrule,'id'=>$id];
        $this->html .= $description.'<input id="'.$this->prefix.$id.'" type="'.$type.'"';
        if($cssclass) $this->html .= ' class="'.$cssclass.'"';
        $this->html .= '><br>';
    }
    function setSucsess($jsdata)
    {
        $this->jsfunc = $jsdata;
    }
    function addButton($id,$value,$cssclass=null)
    {
        $this->buttonid = $id;
        $this->html .= '<input id="'.$this->prefix.$id.'" type="submit" value="'.$value.'"';
        if($cssclass) $this->html .= ' class="'.$cssclass.'"';
        $this->html .= '><br>';
    }
    function end()
    {
        echo $this->html;
        echo '<script>';
        $userid = 0;
        if(app::$user) $userid = app::$user->id;
        $cfrf_key = $this->csrf_key;
        if(!$cfrf_key) $cfrf_key=$this->prefix;
        $urlpath = $this->url.'&csrf-token='.ajaxHelper::newCSRFToken($this->csrf_key,$userid);
        echo 'var '.$this->prefix.$this->buttonid.' = $("#'.$this->prefix.$this->buttonid.'");';
        echo  "\n";
        
        foreach($this->ids as $v)
        {
            echo 'var '.$this->prefix.$v['id'].' = $("#'.$this->prefix.$v['id'].'");';
            $urlpath .= '&'.$v['id'].'="+'.$this->prefix.$v['id'].'[0].value+"';
            echo  "\n";
            if($v['rule'])
            {
                echo $this->prefix.$v['id'].'.on(\'focusout\',function() {';
                echo  "\n";
                if($v['rule'] == 'integer')
                {
                    echo 'if(isNaN('.$this->prefix.$v['id'].'[0].value))';
                }
                echo $this->prefix.$v['id'].'.css(\'background-color\',\'red\');';
                echo  "\n";
                echo ' else ';
                echo  "\n";
                echo $this->prefix.$v['id'].'.css(\'background-color\',\'\');';
                echo  "\n";
                echo '});';
            }
        }
        echo $this->prefix.$this->buttonid.'.on(\'click\',function() {';
        echo  "\n";
        echo  "\n";
        echo 'apirequest("'.$urlpath.'",function(data)
        {
            '.$this->jsfunc.'
        });';
        echo  "\n";
        echo '});';
        
        echo '</script>';
    }
}
