<?php
abstract class Controller
{
    abstract function request($args);
    public function _csrf_formkey($a)
    {
        return $a;
    }
}
abstract class Action
{
    
}
abstract class AbstractUser
{
    public $login;
    public $id;
    public $email;
    public $token;
    public $tokenid;
    public $arr;
    public $isAuth;
    public $permissions;
    public $flags;
    abstract public function auth($login,$pass);
    abstract public function close();
    abstract public function isSuperuser();
    abstract public function isPermission($group);
    abstract public function addPermission($group);
    abstract public function rmPermission($group);
    abstract public function getAllPermissions();
    abstract public static function getById($id);
    abstract public static function verify($id,$token);
    abstract public function reg($login,$passwordhash,$email);
    abstract public function setPassword($newpass);
    abstract public function createToken();
    abstract public function addSession($token);
    abstract public function getSessions();
    abstract public function deleteToken($id);
}
class appException extends Exception
{
}
class сlassNotLoadedException extends appException
{
}
class FileNotFoundException extends appException
{
}
class VisualException extends appException
{
    
}
