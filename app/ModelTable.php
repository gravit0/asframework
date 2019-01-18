<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use db\PDOConnect;

/**
 * Description of ModelTable
 *
 * @author gravit
 */
class ModelTable
{
    //put your code here
    public $table;

    public function __construct()
    {
        if (!app::$db) app::$db = new PDOConnect;
        $_table = get_class($this);
        $pos = strpos($_table, 'Model');
        $this->table = substr($_table, 0, $pos);
    }

    public function getById($id, $column = 'id')
    {
        $table = $this->table;
        $results = app::$db->prepare("SELECT * FROM $table WHERE $column = :id LIMIT 1");
        $results->bindParam(':id', $id, PDO::PARAM_INT);
        $results->execute();
        $results = $results->fetch(PDO::FETCH_ASSOC);
        $obj = new ModelView;
        foreach ($results as $k => $v) {
            $obj->$k = $v;
        }
        return $obj;
    }

    public function select($wherestr, $vars)
    {
        $table = $this->table;
        $results = app::$db->prepare("SELECT * FROM $table WHERE $wherestr");
        foreach ($vars as $k => $v) {
            $results->bindParam($k, $v, PDO::PARAM_STR);
        }
        $results->execute();
        $results = $results->fetchAll(PDO::FETCH_ASSOC);
        return $results;
    }
}
