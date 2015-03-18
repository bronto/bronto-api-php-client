<?php

/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * 
 * @group object
 * @group contact
 */
class Bronto_Tests_Api_ContactTest extends Bronto_Tests_AbstractTest
{
    /**
     * @covers Bronto_Api_Object::addOrUpdate
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
     * @covers Bronto_Api_Object::addOrUpdate
     */
    public function testAddOrUpdateOneValidContact()
    {
        $contact = array('email' => 'example+onevalid@bronto.com');

        /* @var $contacts Bronto_Api_Rowset */
        $contacts = $this->getObject()->addOrUpdate($contact);

        $this->assertTrue($contacts instanceOf Bronto_Api_Rowset);
        $this->assertEquals(1, $contacts->count());
        $this->assertFalse($contacts->hasErrors());
    }

    /**
     * @covers Bronto_Api_Object::addOrUpdate
     */
    public function testAddOrUpdateOneInvalidContact()
    {
        $contact = array('email' => 'example+oneinvalid@');

        /* @var $contacts Bronto_Api_Rowset */
        $contacts = $this->getObject()->addOrUpdate($contact);

        $this->assertTrue($contacts instanceOf Bronto_Api_Rowset);
        $this->assertEquals(1, $contacts->count());
        $this->assertTrue($contacts->hasErrors());
    }

    /**
     * @covers Bronto_Api_Object::add
     */
    public function testAddOneValidContact()
    {
        $contact = array('email' => 'example+' . time() . '@bronto.com');

        /* @var $contacts Bronto_Api_Rowset */
        $contacts = $this->getObject()->add($contact);

        $this->assertTrue($contacts instanceOf Bronto_Api_Rowset);
        $this->assertEquals(1, $contacts->count());
        $this->assertFalse($contacts->hasErrors(), 'Has error: ' . var_export($contacts->getError(), true));
    }

    /**
     * @covers Bronto_Api_Object::add
     */
    public function testAddOneInvalidContact()
    {
        $contact = array('email' => 'example+oneinvalid@');

        /* @var $contacts Bronto_Api_Rowset */
        $contacts = $this->getObject()->add($contact);

        $this->assertTrue($contacts instanceOf Bronto_Api_Rowset);
        $this->assertEquals(1, $contacts->count());
        $this->assertTrue($contacts->hasErrors());
    }

    /**
     * @return Bronto_Api_Contact
     */
    public function getObject()
    {
        return $this->getApi()->getContactObject();
    }
}
