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
        $apiToken->id = TEST_API_TOKEN;
        $apiToken->read();

        $this->assertInstanceOf('Bronto_Api_ApiToken_Row', $apiToken);
        $this->assertSame(TEST_API_TOKEN, $apiToken->id);

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
     * @return Bronto_Api_ApiToken
     */
    public function getObject()
    {
        return $this->getApi()->getApiTokenObject();
    }
}