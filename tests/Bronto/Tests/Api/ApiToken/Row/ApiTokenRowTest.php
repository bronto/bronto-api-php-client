<?php

/**
 * @group row
 * @group apiToken
 */
class Bronto_Tests_Api_ApiToken_Row_ApiTokenRowTest extends Bronto_Tests_AbstractTest
{
    /**
     * @covers Bronto_Api_ApiToken_Row::read
     */
    public function testRead()
    {
        $apiToken = $this->getObject()->createRow();
        $apiToken->id = TEST_API_TOKEN_1;
        $apiToken->read();

        $this->assertInstanceOf('Bronto_Api_ApiToken_Row', $apiToken);
        $this->assertSame(TEST_API_TOKEN_1, $apiToken->id);

        return $apiToken;
    }

    /**
     * @depends testRead
     * @covers Bronto_Api_ApiToken_Row::getAccount
     */
    public function testGetAccount(Bronto_Api_ApiToken_Row $apiToken)
    {
        $account = $apiToken->getAccount();

        $this->assertInstanceOf('Bronto_Api_Account_Row', $account);
    }

    /**
     * @depends testRead
     * @covers Bronto_Api_ApiToken_Row::hasPermissions
     */
    public function testHasPermissions(Bronto_Api_ApiToken_Row $apiToken)
    {
        $this->assertTrue($apiToken->hasPermissions(Bronto_Api_ApiToken::PERMISSION_READ));
        $this->assertTrue($apiToken->hasPermissions(Bronto_Api_ApiToken::PERMISSION_WRITE));
        $this->assertTrue($apiToken->hasPermissions(Bronto_Api_ApiToken::PERMISSION_SEND));
        $this->assertTrue($apiToken->hasPermissions(Bronto_Api_ApiToken::PERMISSIONS_READ_WRITE));
        $this->assertTrue($apiToken->hasPermissions(Bronto_Api_ApiToken::PERMISSIONS_READ_SEND));
        $this->assertTrue($apiToken->hasPermissions(Bronto_Api_ApiToken::PERMISSIONS_WRITE_SEND));
        $this->assertTrue($apiToken->hasPermissions(Bronto_Api_ApiToken::PERMISSIONS_READ_WRITE_SEND));
        $this->assertTrue($apiToken->hasPermissions(Bronto_Api_ApiToken::PERMISSIONS_ALL));
    }

    /**
     * @depends testRead
     * @covers Bronto_Api_ApiToken_Row::getPermissionsLabels
     * @covers Bronto_Api_ApiToken::getPermissionsLabels
     */
    public function testGetPermissionsLabels(Bronto_Api_ApiToken_Row $apiToken)
    {
        $labels = $apiToken->getPermissionsLabels();

        $this->assertSame(array('read', 'write', 'send'), $labels);
    }

    /**
     * @return Bronto_Api_ApiToken
     */
    public function getObject()
    {
        return $this->getApi()->getApiTokenObject();
    }
}