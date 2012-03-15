<?php

abstract class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Bronto_Api
     */
    public function getApi()
    {
        return new Bronto_Api(TEST_API_TOKEN);
    }
}