<?php

namespace db {

    use PDO;
    use app;

    class PDOConnect extends PDO
    {
        function __construct()
        {
            parent::__construct(app::$cfg['db']['connect'], app::$cfg['db']['login'], app::$cfg['db']['password'], []);
        }
    }
}
