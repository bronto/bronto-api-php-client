<?php

spl_autoload_register(function($class) {
    if (0 !== strpos($class, 'Bronto')) {
        return;
    }
    $dir  = realpath(dirname(__DIR__) . '/src/');
    $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    $src  = $dir . DIRECTORY_SEPARATOR . $file;
    if (file_exists($src)) {
        require $src;
    }
});

define('TEST_API_TOKEN', '1EA75F27-87EB-4E45-8D05-4CD1A035ED72');