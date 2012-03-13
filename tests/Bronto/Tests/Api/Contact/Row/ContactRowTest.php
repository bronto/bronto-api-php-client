<?php

/**
 * @group row
 * @group contact
 */
class ContactRowTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Bronto_Api_Contact::createRow
     */
    public function testCreateRow()
    {
        $contact = $this->getObject()->createRow();
        
        $this->assertTrue($contact instanceOf Bronto_Api_Contact_Row);
    }

    /**
     * @return Bronto_Api_Contact
     */
    public function getObject()
    {
        return $this->getApi()->getContactObject();
    }

    /**
     * @return Bronto_Api
     */
    public function getApi()
    {
        return new Bronto_Api(TEST_API_TOKEN);
    }
}