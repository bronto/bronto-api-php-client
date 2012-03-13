<?php

/**
 * @group api
 */
class ApiTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Bronto_Api::login
     */
    public function testEmptyToken()
    {
        $api = new Bronto_Api();
        try {
            $api->login();
        } catch (Exception $e) {
            $this->assertTrue($e instanceOf Bronto_Api_Exception);
            $this->assertTrue($e->getCode() === Bronto_Api_Exception::NO_TOKEN);
        }
    }

    /**
     * @covers Bronto_Api::login
     */
    public function testInvalidToken()
    {
        $api = new Bronto_Api('invalid-token');
        try {
            $api->login();
        } catch (Exception $e) {
            $this->assertTrue($e instanceOf Bronto_Api_Exception);
            $this->assertTrue($e->getCode() === Bronto_Api_Exception::INVALID_TOKEN);
        }
    }

    /**
     * @covers Bronto_Api::login
     */
    public function testValidToken()
    {
        $api = new Bronto_Api(TEST_API_TOKEN);
        $api->login();

        $this->assertTrue($api->isAuthenticated());
    }
}