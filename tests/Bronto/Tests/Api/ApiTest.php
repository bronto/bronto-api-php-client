<?php

/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 * @group api
 */
class Bronto_Tests_Api_ApiTest extends PHPUnit_Framework_TestCase
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
        $api = new Bronto_Api(TEST_API_TOKEN_1);
        $api->login();

        $this->assertTrue($api->isAuthenticated());
    }

    /**
     * @covers Bronto_Api::getTokenInfo
     */
    public function testGetTokenInfo()
    {
        $api = new Bronto_Api(TEST_API_TOKEN_1);
        $apiToken = $api->getTokenInfo();

        $this->assertInstanceOf('Bronto_Api_ApiToken_Row', $apiToken);
        $this->assertSame(TEST_API_TOKEN_1, $apiToken->id);
    }
}
