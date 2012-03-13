<?php

/**
 * @group object
 * @group contact
 */
class ContactTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Bronto_Api_Abstract::addOrUpdate
     * @covers Bronto_Api_Rowset_Abstract::count
     * @covers Bronto_Api_Row_Abstract::hasError
     * @covers Bronto_Api_Row_Abstract::getErrorCode
     */
    public function testAddOrUpdateManyContactsWithSomeInvalid()
    {
        $contacts = array();
        $contacts[] = array('email' => 'example+valid@bronto.com');
        $contacts[] = array('email' => 'example+invalidbronto.com');
        $contacts[] = array('email' => 'example+another-invalidbronto.com');
        $contacts[] = array('email' => 'example+another-valid@bronto.com');

        /* @var $contacts Bronto_Api_Rowset */
        $contacts = $this->getObject()->addOrUpdate($contacts);

        $this->assertTrue($contacts instanceOf Bronto_Api_Rowset);
        $this->assertEquals(4, $contacts->count());
        $this->assertTrue($contacts->hasErrors());

        foreach ($contacts as $i => $contact /* @var $contact Bronto_Api_Contact_Row */) {
            switch ($i) {
                case 0:
                case 3:
                    $this->assertNotEmpty($contact->id);
                    $this->assertFalse($contact->hasError());
                    break;
                case 1:
                case 2:
                    $this->assertEmpty($contact->id);
                    $this->assertTrue($contact->hasError());
                    $this->assertEquals(Bronto_Api_Contact_Exception::INVALID_EMAIL, $contact->getErrorCode());
                    break;
            }
        }
    }

    /**
     * @covers Bronto_Api_Abstract::addOrUpdate
     * @covers Bronto_Api_Row_Abstract::hasError
     * @covers Bronto_Api_Row_Abstract::getErrorCode
     */
    public function testAddOrUpdateOneValidContact()
    {
        $contact = array('email' => 'example+onevalid@bronto.com');
        $contact = $this->getObject()->addOrUpdate($contact);

        $this->assertTrue($contact instanceOf Bronto_Api_Contact_Row);
        $this->assertNotEmpty($contact->id);
        $this->assertFalse($contact->hasError(), 'Contact has error: ' . $contact->getErrorMessage());
    }

    /**
     * @covers Bronto_Api_Abstract::addOrUpdate
     * @covers Bronto_Api_Row_Abstract::hasError
     * @covers Bronto_Api_Row_Abstract::getErrorCode
     */
    public function testAddOrUpdateOneInvalidContact()
    {
        $contact = array('email' => 'example+oneinvalid@');
        $contact = $this->getObject()->addOrUpdate($contact);

        $this->assertTrue($contact instanceOf Bronto_Api_Contact_Row);
        $this->assertEmpty($contact->id);
        $this->assertTrue($contact->hasError());
        $this->assertEquals(Bronto_Api_Contact_Exception::INVALID_EMAIL, $contact->getErrorCode());
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