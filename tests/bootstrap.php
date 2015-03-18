<?php
/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 */
if (file_exists($file = __DIR__.'/../vendor/autoload.php')) {
    $loader = require $file;
    $loader->set('Bronto_Tests_', __DIR__ . '/');
} else if (file_exists($file = __DIR__.'/../autoload.php')) {
    require_once $file;
} elseif (file_exists($file = __DIR__.'/../autoload.php.dist')) {
    require_once $file;
}

if (!((bool) TEST_API_TOKEN_1)) {
    throw new RuntimeException('You must specify an API token to test with in phpunit.xml');
}
