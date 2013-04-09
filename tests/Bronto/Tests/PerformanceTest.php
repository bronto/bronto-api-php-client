<?php

/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 * @group performance
 */
class Bronto_Tests_PerformanceTest extends PHPUnit_Framework_TestCase
{
    public function testApiTokenReadPerformance()
    {
        $time = microtime(true);
        for ($i = 0, $iterations = 10; $i < $iterations; $i++) {
            $api = new Bronto_Api(TEST_API_TOKEN_1);
            $apiToken = $api->getApiTokenObject()->createRow();
            $apiToken->id = TEST_API_TOKEN_1;
            $apiToken->read();
        }
        $time = microtime(true) - $time;

        $this->printResults('Bronto_Api_ApiToken_Row::read', $time, $iterations);
    }

    private function printResults($test, $time, $iterations)
    {
        if (0 == $iterations) {
            throw new InvalidArgumentException('Iterations cannot be zero.');
        }

        $title          = "{$test} results:\n";
        $iterationsText = sprintf("Iterations:         %d\n", $iterations);
        $totalTime      = sprintf("Total Time:         %.3f s\n", $time);
        $iterationTime  = sprintf("Time per iteration: %.3f ms\n", $time / $iterations * 1000);

        $max = max(strlen($title), strlen($iterationTime)) - 1;

        echo "\n" . str_repeat('-', $max) . "\n";
        echo $title;
        echo str_repeat('=', $max) . "\n";
        echo $iterationsText;
        echo $totalTime;
        echo $iterationTime;
        echo str_repeat('-', $max) . "\n";
    }
}
