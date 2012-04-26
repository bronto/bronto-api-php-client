<?php

/**
 * @group row
 * @group contact
 */
class Bronto_Tests_Api_Row_ContactRowTest extends Bronto_Tests_AbstractTest
{
    /**
     * @covers Bronto_Api_Contact_Row::read
     * @expectedException Bronto_Api_Contact_Exception
     */
    public function testReadEmptyContact()
    {
        $contact = $this->getObject()->createRow();
        $contact->read();
    }

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
    }

    /**
     * @covers Bronto_Api_Contact_Row::save
     * @covers Bronto_Api_Contact_Exception::__construct
     */
    public function testSaveInvalidContactCaughtException()
    {
        /* @var $contact Bronto_Api_Contact_Row */
        $contact = $this->getObject()->createRow();
        $contact->email = 'example+newrowinvalid';
        try {
            $contact->save();
        } catch (Bronto_Api_Contact_Exception $e) {
            //
        }

        $this->assertEmpty($contact->id);
        $this->assertTrue($contact instanceOf Bronto_Api_Contact_Row);
        $this->assertTrue($contact->hasError());
        $this->assertTrue($contact->isReadOnly());

        return $contact;
    }

    /**
     * @covers Bronto_Api_Contact_Row::save
     * @covers Bronto_Api_Contact_Exception::__construct
     * @expectedException Bronto_Api_Row_Exception
     * @depends testSaveInvalidContactCaughtException
     */
    public function testDoubleSaveInvalidContact(Bronto_Api_Contact_Row $contact)
    {
        $contact->save();
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
    }

    /**
     * @covers Bronto_Api_Contact_Row::save
     */
    public function testDoubleSaveContact()
    {
        /* @var $contact Bronto_Api_Contact_Row */
        $contact = $this->getObject()->createRow();
        $contact->email = 'example+doublesave' . time(). '@bronto.com';
        $result = $contact->save();

        $this->assertTrue($contact instanceOf Bronto_Api_Contact_Row);
        $this->assertSame(spl_object_hash($contact), spl_object_hash($result));
        $this->assertNotEmpty($contact->id);
        $this->assertFalse($contact->isReadOnly());
        $this->assertFalse($contact->hasError(), 'Contact has error: ' . $contact->getErrorMessage());

        $result = $contact->save();

        $this->assertTrue($contact instanceOf Bronto_Api_Contact_Row);
        $this->assertSame(spl_object_hash($contact), spl_object_hash($result));
        $this->assertNotEmpty($contact->id);
        $this->assertFalse($contact->isReadOnly());
        $this->assertFalse($contact->hasError(), 'Contact has error: ' . $contact->getErrorMessage());
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
        $this->assertFalse($contact->isReadOnly());
        $this->assertFalse($contact->hasError(), 'Contact has error: ' . $contact->getErrorMessage());
        $this->assertTrue($contact->isNew());

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
        $this->assertFalse($contact->isReadOnly());
        $this->assertFalse($contact->hasError(), 'Contact has error: ' . $contact->getErrorMessage());
        $this->assertFalse($contact->isNew());

        return $contact;
    }

    /**
     * @covers Bronto_Api_Contact_Row::delete
     * @depends testReadContact
     */
    public function testDeleteContactAfterRead(Bronto_Api_Contact_Row $contact)
    {
        $contact->delete();

        $this->assertEquals(1, count($contact->getData()));
        $this->assertTrue($contact->isReadOnly());
        $this->assertFalse($contact->hasError(), 'Contact has error: ' . $contact->getErrorMessage());
        $this->assertFalse($contact->isNew());

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
    }

    /**
     * @covers Bronto_Api_Contact_Row::save
     * @depends testDeleteContactAfterRead
     */
    public function testSaveContactAfterDeleteCaughtException(Bronto_Api_Contact_Row $contact)
    {
        $contact->status = 'transactional';
        try {
            $contact->save();
        } catch (Bronto_Api_Row_Exception $e) {
            //
        }

        $this->assertEquals(1, count($contact->getData()));
        $this->assertTrue($contact->isReadOnly());
        $this->assertFalse($contact->hasError(), 'Contact has error: ' . $contact->getErrorMessage());
        $this->assertFalse($contact->isNew());
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
    }

    /**
     * @covers Bronto_Api_Contact_Row::save
     * @covers Bronto_Api_Contact_Exception::__construct
     */
    public function testSaveContactWhoIsOnSuppressionListCaughtException()
    {
        /* @var $contact Bronto_Api_Contact_Row */
        $contact = $this->getObject()->createRow();
        $contact->email = 'kendrajanelle@mailinator.com';
        try {
            $contact->save();
        } catch (Bronto_Api_Contact_Exception $e) {
            //
        }

        $this->assertTrue($contact->isReadOnly());
        $this->assertTrue($contact->hasError());
        $this->assertFalse($contact->isNew());
    }

    /**
     * @covers Bronto_Api_Contact_Row::setField
     */
    public function testSetFieldWithFieldId()
    {
        /* @var $contact Bronto_Api_Contact_Row */
        $contact = $this->getObject()->createRow();
        $contact->setField('0bbd03e900000000000000000000000106ce', 'test');

        $this->assertEquals('0bbd03e900000000000000000000000106ce', $contact->fields[0]['fieldId']);
        $this->assertEquals('test', $contact->fields[0]['content']);
        $this->assertEquals('test', $contact->getField('0bbd03e900000000000000000000000106ce'));
    }

    /**
     * @return Bronto_Api_Contact
     */
    public function getObject()
    {
        return $this->getApi()->getContactObject();
    }
}