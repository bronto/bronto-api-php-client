<?php

/**
 * @group object
 * @group account
 */
class Bronto_Tests_Api_Account_AccountTest extends Bronto_Tests_AbstractTest
{
    /**
     * @covers Bronto_Api_Account::readAll
     */
    public function testReadAllAccounts()
    {
        $rowset = $this->getObject()->readAll();

        $this->assertGreaterThan(0, $rowset->count(), 'No Accounts were found.');
    }

    /**
     * @return Bronto_Api_Account
     */
    public function getObject()
    {
        return $this->getApi()->getAccountObject();
    }
}