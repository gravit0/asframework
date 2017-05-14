<?php
namespace helpers;
class consoleHelper
{
    static function textColor($text,$color)
    {
        return "\x1b[".$color."m".$text."\x1b[0m";
    }
    static function setColor($color)
    {
        return "\x1b[".$color."m";
    }
    const ATTR_NORMAL = 0;
    const ATTR_BOLD = 1;
    const ATTR_UNDERLINE = 4;
    const ATTR_FLASH = 5;
    const ATTR_NEGATIVE = 7;
    const ATTR_INVISIBLE = 8;
    
    const TEXTCOLOR_BLACK = 30;
    const TEXTCOLOR_RED = 31;
    const TEXTCOLOR_GREEN = 32;
    const TEXTCOLOR_YELLOW = 33;
    const TEXTCOLOR_BLUE = 34;
    const TEXTCOLOR_PURPURE = 35;
    const TEXTCOLOR_BLUE2 = 36;
    const TEXTCOLOR_CLEAR = 37;
}
