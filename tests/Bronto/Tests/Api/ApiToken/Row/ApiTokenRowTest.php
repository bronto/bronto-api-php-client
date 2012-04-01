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
    }

    /**
     * @return Bronto_Api_ApiToken
     */
    public function getObject()
    {
        return $this->getApi()->getApiTokenObject();
    }
}