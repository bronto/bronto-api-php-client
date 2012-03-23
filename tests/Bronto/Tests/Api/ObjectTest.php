<?php

/**
 * @group object
 */
class Bronto_Tests_Api_ObjectTest extends Bronto_Tests_AbstractTest
{
    public function testFlushWithoutWork()
    {
        $contactObject = $this->getObject();
        $contactObject->flush();
    }

    /**
     * @covers Bronto_Api_Row::persist
     * @covers Bronto_Api_Object::addToWriteCache
     * @covers Bronto_Api_Object::getWriteCacheSize
     */
    public function testPersistDoesNotDuplicate()
    {
        $contactObject = $this->getObject();

        $contact1 = $contactObject->createRow();
        $contact1->email = 'persist1@example.com';
        $contact1->persist();

        $contact2 = $contactObject->createRow();
        $contact2->email = 'persist2@example.com';
        $contact2->persist();

        $contact3 = $contactObject->createRow();
        $contact3->email = 'persist3@example.com';
        $contact3->persist();

        $writeCache = $contactObject->getWriteCache();
        $this->assertEquals(3, $contactObject->getWriteCacheSize());
        $this->assertEquals(3, count($writeCache['addOrUpdate']));
        $this->assertEquals(1, count($writeCache['addOrUpdate']['persist1@example.com']));

        $contact1 = $contactObject->createRow();
        $contact1->email  = 'persist1@example.com';
        $contact1->status = 'changed';
        $contact1->persist();

        $writeCache = $contactObject->getWriteCache();
        $this->assertEquals(3, $contactObject->getWriteCacheSize());
        $this->assertEquals(3, count($writeCache['addOrUpdate']));
        $this->assertEquals(2, count($writeCache['addOrUpdate']['persist1@example.com']));
    }

    /**
     * @covers Bronto_Api_Object::flush
     */
    public function testFlush()
    {
        $contactObject = $this->getObject();

        $contact1 = $contactObject->createRow();
        $contact1->email = 'persist1@example.com';
        $contact1->persist();

        $contact2 = $contactObject->createRow();
        $contact2->email = 'persist2@example.com';
        $contact2->persist();

        $result = $contactObject->flush();

        $writeCache = $contactObject->getWriteCache();
        $this->assertTrue($result instanceOf Bronto_Api_Rowset);
        $this->assertEquals(0, $contactObject->getWriteCacheSize());
        $this->assertEquals(0, count($writeCache['addOrUpdate']));
    }

    /**
     * @covers Bronto_Api_Object::__call
     * @expectedException BadMethodCallException
     */
    public function testInvalidMethod()
    {
        $this->getObject()->invalidMethod();
    }

    /**
     * @return Bronto_Api_Contact
     */
    public function getObject()
    {
        return $this->getApi()->getContactObject();
    }
}