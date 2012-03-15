<?php

/**
 * @group api
 */
class ApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Bronto_Api::login
     * @covers Bronto_Api::isAuthenticated
     * @expectedException Bronto_Api_Exception
     * @expectedExceptionCode 99002
     */
    public function testEmptyToken()
    {
        $api = new Bronto_Api();
        $api->login();

        $this->assertFalse($api->isAuthenticated());
    }

    /**
     * @covers Bronto_Api::login
     * @covers Bronto_Api::isAuthenticated
     * @expectedException Bronto_Api_Exception
     * @expectedExceptionCode 102
     */
    public function testInvalidToken()
    {
        $api = new Bronto_Api('invalid-token');
        $api->login();

        $this->assertFalse($api->isAuthenticated());
    }

    /**
     * @covers Bronto_Api::login
     * @covers Bronto_Api::isAuthenticated
     */
    public function testValidToken()
    {
        $api = new Bronto_Api(TEST_API_TOKEN);
        $api->login();

        $this->assertTrue($api->isAuthenticated());
    }
}