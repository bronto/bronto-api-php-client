<?php

if (file_exists($file = __DIR__.'/../autoload.php')) {
    require_once $file;
} elseif (file_exists($file = __DIR__.'/../autoload.php.dist')) {
    require_once $file;
}

if (!((bool) TEST_API_TOKEN_1)) {
    throw new RuntimeException('You must specify an API token to test with in phpunit.xml');
}