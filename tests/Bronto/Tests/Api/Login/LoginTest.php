<?php

/**
 * @group object
 * @group login
 */
class Bronto_Tests_Api_Login_LoginTest extends Bronto_Tests_AbstractTest
{
    /**
     * @covers Bronto_Api_Login::readAll
     */
    public function testReadAllLogins()
    {
        $rowset = $this->getObject()->readAll();

        $this->assertGreaterThan(0, $rowset->count(), 'No Logins were found.');

        $login = $rowset->current();

        $this->assertInstanceOf('Bronto_Api_Login_Row', $login);
    }

    /**
     * @return Bronto_Api_Login
     */
    public function getObject()
    {
        return $this->getApi()->getLoginObject();
    }
}