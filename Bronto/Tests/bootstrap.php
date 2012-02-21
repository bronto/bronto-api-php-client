<?php

spl_autoload_register(function($class) {
    $dir  = dirname(__DIR__);
    $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
    $src  = $dir . DIRECTORY_SEPARATOR . $file;
    if (file_exists($src)) {
        require $src;
    }
});