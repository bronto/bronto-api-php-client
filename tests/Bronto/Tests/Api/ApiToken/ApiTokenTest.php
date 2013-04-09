<?php

/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 * @group object
 * @group apiToken
 */
class Bronto_Tests_Api_ApiToken_ApiTokenTest extends Bronto_Tests_AbstractTest
{
    /**
     * @covers Bronto_Api_ApiToken::readAll
     */
    public function testReadAllTokens()
    {
        $rowset = $this->getObject()->readAll();

        $this->assertGreaterThan(0, $rowset->count());
    }

    /**
     * @return Bronto_Api_ApiToken
     */
    public function getObject()
    {
        return $this->getApi()->getApiTokenObject();
    }
}
