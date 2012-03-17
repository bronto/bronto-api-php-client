<?php

namespace Bronto\Tests\Api\Contact\Row;

use Bronto\Tests\AbstractTest;
use \Bronto_Api_Contact_Row;

/**
 * @group row
 * @group contact
 */
class ContactRowTest extends AbstractTest
{
    /**
     * @covers Bronto_Api_Contact_Row::save
     * @covers Bronto_Api_Contact_Exception::__construct
     * @expectedException Bronto_Api_Contact_Exception
     * @expectedExceptionCode 303
     */
    public function testSaveInvalidContact()
    {
        /* @var $contact Bronto_Api_Contact_Row */
        $contact = $this->getObject()->createRow();
        $contact->email = 'example+newrowinvalid';
        $contact->save();

        $this->assertEmpty($contact->id);
        $this->assertTrue($contact instanceOf Bronto_Api_Contact_Row);
    }

    /**
     * @covers Bronto_Api_Contact_Row::delete
     * @covers Bronto_Api_Contact_Exception::__construct
     * @expectedException Bronto_Api_Contact_Exception
     */
    public function testDeleteInvalidContact()
    {
        /* @var $contact Bronto_Api_Contact_Row */
        $contact = $this->getObject()->createRow();
        $contact->email = 'example+newrowinvalid';
        $contact->delete();

        $this->assertEmpty($contact->id);
        $this->assertTrue($contact instanceOf Bronto_Api_Contact_Row);
    }

    /**
     * @covers Bronto_Api_Contact_Row::save
     */
    public function testCreateContact()
    {
        /* @var $contact Bronto_Api_Contact_Row */
        $contact = $this->getObject()->createRow();
        $contact->email = 'example+newrow' . time(). '@bronto.com';
        $result = $contact->save();

        $this->assertTrue($contact instanceOf Bronto_Api_Contact_Row);
        $this->assertSame(spl_object_hash($contact), spl_object_hash($result));
        $this->assertNotEmpty($contact->id);
        $this->assertFalse($contact->hasError(), 'Contact has error: ' . $contact->getErrorMessage());

        return $contact;
    }

    /**
     * @covers Bronto_Api_Contact_Row::read
     * @depends testCreateContact
     */
    public function testReadContact(Bronto_Api_Contact_Row $contact)
    {
        $result = $contact->read();

        $this->assertTrue($contact instanceOf Bronto_Api_Contact_Row);
        $this->assertSame(spl_object_hash($contact), spl_object_hash($result));
        $this->assertNotEmpty($contact->id);
        $this->assertFalse($contact->hasError(), 'Contact has error: ' . $contact->getErrorMessage());

        return $contact;
    }

    /**
     * @covers Bronto_Api_Contact_Row::delete
     * @depends testReadContact
     */
    public function testDeleteContactAfterRead(Bronto_Api_Contact_Row $contact)
    {
        $contact->delete();

        $this->assertTrue($contact->isReadOnly());
        $this->assertEquals(1, count($contact->getData()));

        return $contact;
    }

    /**
     * @covers Bronto_Api_Contact_Row::save
     * @expectedException Bronto_Api_Row_Exception
     * @depends testDeleteContactAfterRead
     */
    public function testSaveContactAfterDelete(Bronto_Api_Contact_Row $contact)
    {
        $contact->status = 'transactional';
        $contact->save();

        $this->assertTrue($contact->isReadOnly());
        $this->assertEquals(1, count($contact->getData()));
    }

    /**
     * @covers Bronto_Api_Contact_Row::save
     * @covers Bronto_Api_Contact_Exception::__construct
     * @expectedException Bronto_Api_Contact_Exception
     * @expectedExceptionCode 315
     */
    public function testSaveContactWhoIsOnSuppressionList()
    {
        /* @var $contact Bronto_Api_Contact_Row */
        $contact = $this->getObject()->createRow();
        $contact->email = 'kendrajanelle@mailinator.com';
        $contact->save();

        $this->assertEmpty($contact->id);
        $this->assertTrue($contact instanceOf Bronto_Api_Contact_Row);
    }

    /**
     * @return Bronto_Api_Contact
     */
    public function getObject()
    {
        return $this->getApi()->getContactObject();
    }
}