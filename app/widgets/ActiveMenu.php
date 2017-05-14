<?php
namespace widgets;
class ActiveMenu
{
    public $elements;
    function add($text,$link,$id,$active=false)
    {
        $this->elements[] = ['text'=>$text, 'link'=>$link, 'id'=>$id, 'active'=>$active];
    }
    function setActive($id,$active=true)
    {
        foreach($this->elements as &$v)
        {
            if($id == $v['id'])
            {
                $v['active'] = $active;
                break;
            }
        }
    }
    function printHtml()
    {
        echo '<ul class="ActiveMenu">';
        foreach($this->elements as $v)
        {
            echo '<li';
            if($v['active']) echo ' class="active"';
            echo '><a href="';
            echo $v['link'];
            echo '">';
            echo $v['text'];
            echo '</a></li>';
        }
        echo '</ul>';
    }
}
