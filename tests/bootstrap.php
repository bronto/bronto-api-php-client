<?php

if (file_exists($file = __DIR__.'/../autoload.php')) {
    require_once $file;
} elseif (file_exists($file = __DIR__.'/../autoload.php.dist')) {
    require_once $file;
}

define('TESTS_BASEPATH', realpath(dirname(__FILE__) . '/Bronto/Tests'));
define('TEST_API_TOKEN', '4EEE0D9E-101E-4E15-99D5-DF4648199C18');