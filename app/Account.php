<?php
use db\PDOConnect;
class AccountException extends appException
{
    const AuthError = 1;
    const NoLoginError = 2;
    const FatalBanError = 3;
}
class Account extends AbstractUser
{
    function auth($login,$pass)
    {
        if(!app::$db) app::$db = new PDOConnect;
        $results = app::$db->prepare('SELECT * FROM users WHERE login = :login LIMIT 1');
        $results->bindParam(':login', $login, PDO::PARAM_STR);
        $results->execute();
        $results = $results->fetch(PDO::FETCH_ASSOC);
        if($results)
        {
            $dbpass = $results['passwd'];
            if(!password_verify($pass,$dbpass)) throw new AccountException(AccountException::AuthError);
            foreach($results as $key => $v)
            {
                $this->$key = $v;
            }
            $this->permissions = (integer) $this->permissions;
            $this->flags = (integer) $this->flags;
            if($this->flags & FLAG_NOLOGIN) throw new AccountException(AccountException::NoLoginError);
            if($this->flags & FLAG_FATALBAN) throw new AccountException(AccountException::FatalBanError);
            $this->id = (integer) $this->id;
            $this->isAuth = true;
            $token = $this->createToken();
            $this->tokenid = $this->addSession($token);
            $this->token = $token;
        }
        else throw new AccountException(AccountException::AuthError);
    }
    function close()
    {
        if($this->tokenid) $this->deleteToken($this->tokenid);
        $this->isAuth = false;
    }
    function getAllPermissions()
    {
        $result = [];
        $groupmap = app::$cfg['users']['groupmap'];
        foreach($groupmap as $key => $v)
        {
            if($this->permissions & $key) $result[] = $v;
        }
        return $result;
    }
    function isSuperuser()
    {
        return $this->permissions & PERM_SUPERUSER;
    }
    function isPermission($perm)
    {
        return $this->permissions & $perm;
    }
    function addPermission($group)
    {
        $this->permissions = $this->permissions | $group;
    }
    function rmPermission($group)
    {
        $this->permissions = $this->permissions ^ $group;
    }
    function isFlag($flag)
    {
        return $this->flags & $flag;
    }
    function addFlag($group)
    {
        $this->flags = $this->flags | $group;
    }
    function rmFlag($group)
    {
        $this->flags = $this->flags ^ $group;
    }
    function setPassword($pass)
    {
        $results = app::$db->prepare('UPDATE `users` SET `passwd` = :pass WHERE `id` = :id');
        $results->bindParam(':id', $this->id, PDO::PARAM_INT);
        $hash = password_hash($pass,PASSWORD_DEFAULT);
        $results->bindParam(':pass', $hash , PDO::PARAM_STR);
        $results->execute();
    }
    function pushPermissions()
    {
        $results = app::$db->prepare('UPDATE `users` SET `permissions` = :perm WHERE `id` = :id');
        $results->bindParam(':id', $this->id, PDO::PARAM_INT);
        $results->bindParam(':perm', $this->permissions , PDO::PARAM_INT);
        $results->execute();
    }
    function pushFlags()
    {
        $results = app::$db->prepare('UPDATE `users` SET `flags` = :perm WHERE `id` = :id');
        $results->bindParam(':id', $this->id, PDO::PARAM_INT);
        $results->bindParam(':perm', $this->flags , PDO::PARAM_INT);
        $results->execute();
    }
    static function getById($id)
    {
        if(!app::$db)app:: $db = new PDOConnect;
        $results = app::$db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $results->bindParam(':id', $id, PDO::PARAM_INT);
        $results->execute();
        $results = $results->fetch(PDO::FETCH_ASSOC);
        if($results)
        {
            $acc = new Account;
            foreach($results as $key => $v)
            {
                $acc->$key = $v;
            }
            $acc->permissions = (integer) $acc->permissions;
            $acc->flags = (integer) $acc->flags;
            $acc->id = (integer) $acc->id;
            $acc->isAuth = true;
            return $acc;
        }
    }
    static function getByToken()
    {
        $token = $_COOKIE['auth_token'];
        $tokenid = $_COOKIE['auth_tokenid'];
        $id_user = Account::verify($tokenid,$token);
        if(!$id_user) return null;
        else
        {
            $account = Account::getById($id_user);
            $account->tokenid =(integer)  $tokenid;
            $account->token = $token;
            return $account;
        }
    }
    static function getByLogin($login)
    {
        if(!app::$db)app:: $db = new PDOConnect;
        $results = app::$db->prepare('SELECT * FROM users WHERE login = :login LIMIT 1');
        $results->bindParam(':login', $login, PDO::PARAM_STR);
        $results->execute();
        $results = $results->fetch(PDO::FETCH_ASSOC);
        if($results)
        {
            $acc = new Account;
            foreach($results as $key => $v)
            {
                $acc->$key = $v;
            }
            $acc->permissions = (integer) $acc->permissions;
            $acc->flags = (integer) $acc->flags;
            $acc->id = (integer) $acc->id;
            $acc->isAuth = true;
            return $acc;
        }
    }
    static function verify($id,$token)
    {
        if(!app::$db)app:: $db = new PDOConnect;
        $results = app::$db->prepare('SELECT * FROM sessions WHERE id = :id LIMIT 1');
        $results->bindParam(':id', $id, PDO::PARAM_INT);
        $results->execute();
        $results = $results->fetch(PDO::FETCH_ASSOC);
        if($results)
        {
            if($token === $results['token']) return $results['user_id'];
            else return false;
        }
        else return false;
    }
    function reg($login,$passwordhash,$email)
    {
        $results = app::$db->prepare('INSERT INTO `users` (`login`, `passwd`, `email`, `permissions`, `flags`) VALUES ( :login, :pass, :email, 0, 0)');
        //$results->bindParam(':userid', $this->id, PDO::PARAM_INT);
        $results->bindParam(':login', $login, PDO::PARAM_STR);
        $results->bindParam(':pass', $passwordhash, PDO::PARAM_STR);
        $results->bindParam(':email', $email, PDO::PARAM_STR);
        $results->execute();
    }
    function createToken()
    {
        $chars = 'abcdefhiknrstyzABCDEFGHKNQRSTYZ1234567890';
        $numChars = strlen($chars);
        $string = '';
        for ($i = 0; $i < 30; $i++) {
            $string .= substr($chars, rand(1, $numChars) - 1, 1);
        }
        return $string;
    }
    function addSession($token,$ip = null)
    {
        $results = app::$db->prepare('INSERT INTO `sessions` (`token`, `ip`, `user_id`) VALUES ( :token , :ip, :userid)');
        $results->bindParam(':userid', $this->id, PDO::PARAM_INT);
        $results->bindParam(':token', $token, PDO::PARAM_STR);
        $binIP = null;
        if($ip) $binIP = $ip;
        else $binIP = app::$request->ip;
        $results->bindParam(':ip', $ip, PDO::PARAM_STR);
        $results->execute();
        return app::$db->lastInsertId();
    }
    function getSessions()
    {
        $results = app::$db->prepare('SELECT * FROM sessions WHERE user_id = :id');
        $results->bindParam(':id', $this->id, PDO::PARAM_INT);
        $results->execute();
        $results = $results->fetchAll(PDO::FETCH_ASSOC);
        if($results)
        {
            return $results;
        }
        else return [];
    }
    function deleteToken($id)
    {
        $results = app::$db->prepare('DELETE FROM `sessions` WHERE `id` = :id');
        $results->bindParam(':id', $id, PDO::PARAM_INT);
        $results->execute();
    }
}
