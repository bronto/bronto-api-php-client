<?php

if (file_exists($file = __DIR__.'/../autoload.php')) {
    require_once $file;
} elseif (file_exists($file = __DIR__.'/../autoload.php.dist')) {
    require_once $file;
}

define('TESTS_BASEPATH', realpath(dirname(__FILE__) . '/Bronto/Tests'));
define('TEST_API_TOKEN', '1EA75F27-87EB-4E45-8D05-4CD1A035ED72');

if (!function_exists('d')) {

    /**
     * Dump
     *
     * @param mixed $thing to be dumped
     * @param int   $i     the index in the stack trace to report as here-ish
     */
    function d($thing, $i = 0)
    {
        $trace = debug_backtrace();
        $lines = array();

        for ($j = $i; $j <= ($i + 1); $j++) {
            if (isset($trace[$j]['line'])) {
                $lines[] = "Line <b>{$trace[$j]['line']}</b> of <b>{$trace[$j]['file']}</b>";
            }
        }

        if (PHP_SAPI == 'cli') {
            echo "\n\033[1;30m==============================\033[0m";
        } else {
            echo '</script></style><div style="border: 2px solid red; background-color: white; padding: 5px">';
        }

        $count = 1;
        foreach ($lines as $index => $line) {
            if ($count > 1) {
                $line = "    {$line}";
            }
            if (PHP_SAPI == 'cli') {
                echo "\n\033[0;31m{$line}\033[0m";
            } else {
                if ($count > 1) {
                    $line = "&nbsp;&nbsp;&nbsp;{$line}";
                }
                echo "<pre>{$line}</pre>";
            }
            $count++;
        }

        if (PHP_SAPI == 'cli') {
            echo "\n\033[1;30m==============================\033[0m\n";
        }

        if (function_exists('ladybug_dump')) {
            ladybug_dump($thing);
        } else if (extension_loaded('xdebug')) {
            var_dump($thing);
        } else {
            echo '<pre>';
            var_dump($thing);
            echo '</pre>';
        }

        if (PHP_SAPI == 'cli') {
            echo "\n\n";
        } else {
            echo '</div><br>';
        }
    }

    /**
     * Dump and die.
     *
     * @param mixed $thing to be dumped
     */
    function dd($thing)
    {
        d($thing, 1);
        exit;
    }
}