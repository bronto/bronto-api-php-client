<?php

if (file_exists($file = __DIR__.'/../autoload.php')) {
    require_once $file;
} elseif (file_exists($file = __DIR__.'/../autoload.php.dist')) {
    require_once $file;
}

define('TESTS_BASEPATH', realpath(dirname(__FILE__) . '/Bronto/Tests'));
define('TEST_API_TOKEN', '1EA75F27-87EB-4E45-8D05-4CD1A035ED72');