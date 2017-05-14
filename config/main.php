<?php
const DirSite = '/srv/http/as/';
const DirCSS = '/data/css/';
const DirJS = '/data/js/';
const cfg_class_logger = '\\loggers\\FileLogger';

const DebugMode = true;

define('PERM_READ', 1 << 0);   // 0001
define('PERM_MODER', 1 << 1); // 0010
define('PERM_ADMIN', 1 << 2);   // 0100
define('PERM_SUPERUSER', 1 << 3); // 1000

define('FLAG_HIDDEN', 1 << 0); // 0001
define('FLAG_SYSTEM', 1 << 1); // 0010
define('FLAG_NOLOGIN', 1 << 2); // 0100
define('FLAG_FATALBAN', 1 << 3); // 1000

const CSRF_SECRET = 'xO6t3QqxslDVKgAXKgJIleWQQwBAvqzn';
