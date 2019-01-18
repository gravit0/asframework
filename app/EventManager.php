<?php
use \db\PDOConnect;
class EventManager
{
    public $events;
    const OPTION_CRICICAL = 1;

    static function getNewEventsReceiver($userid)
    {
        if (!app::$db) app::$db = new PDOConnect;
        $results = app::$db->prepare('SELECT * FROM event WHERE ( id_receiver = :id ) AND ( isNew  = 1 )LIMIT 1');
        $results->bindParam(':id', $userid, PDO::PARAM_INT);
        $results->execute();
        $results = $results->fetchAll(PDO::FETCH_ASSOC);
        $class = new EventManager;
        $class->events = $results;
        return $class;
    }

    static function getEventsReceiver($userid)
    {
        if (!app::$db) app::$db = new PDOConnect;
        $results = app::$db->prepare('SELECT * FROM event WHERE id_receiver = :id LIMIT 1');
        $results->bindParam(':id', $userid, PDO::PARAM_INT);
        $results->execute();
        $results = $results->fetchAll(PDO::FETCH_ASSOC);
        $class = new EventManager;
        $class->events = $results;
        return $class;
    }

    static function getEventsSender($userid)
    {
        if (!app::$db) app::$db = new PDOConnect;
        $results = app::$db->prepare('SELECT * FROM event WHERE id_sender = :id LIMIT 1');
        $results->bindParam(':id', $userid, PDO::PARAM_INT);
        $results->execute();
        $results = $results->fetchAll(PDO::FETCH_ASSOC);
        $class = new EventManager;
        $class->events = $results;
        return $class;
    }

    function isNewEvents()
    {
        $result = false;
        foreach ($this->events as $v) {
            if ($v['isNew']) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    static function sendEvent($idsender, $idreceiver, $type, $options, $data)
    {
        if (!app::$db) app::$db = new PDOConnect;
        $results = app::$db->prepare('INSERT INTO `event` (`id_sender`, `id_receiver`, `type`, `options`, `data`) VALUES (:idsender, :idreceiver, :type, :options, :data)');
        $results->bindParam(':idsender', $idsender, PDO::PARAM_INT);
        $results->bindParam(':idreceiver', $idreceiver, PDO::PARAM_INT);
        $results->bindParam(':type', $type, PDO::PARAM_INT);
        $results->bindParam(':options', $options, PDO::PARAM_INT);
        $results->bindParam(':data', $data, PDO::PARAM_STR);
        $results->execute();
    }
}


