<?php
namespace controllers;
use visual;
use Controller;
use Exception;
class errorController extends Controller
{
    function request($args)
    {
        throw new Exception('CRASH!');
    }
}
