<?php

/**
 * @group object
 * @group activity
 */
class Bronto_Tests_Api_Activity_ActivityTest extends Bronto_Tests_AbstractTest
{
    /**
     * @covers Bronto_Api_Activity::readAll
     */
    public function testActivityContactIdFilter()
    {
        $contactId  = '53a41bc5-e3ce-4691-b9e8-d0d435e0bcb8';
        $activities = $this->getObject()->readAll(null, null, null, $contactId);

        $this->assertGreaterThan(0, $activities->count());
        foreach ($activities as $activity) {
            $this->assertEquals($activity->contactId, $contactId);
        }
    }

    /**
     * @covers Bronto_Api_Activity::readAll
     */
    public function testActivityContactIdFilterWithEmptyType()
    {
        $contactId  = '53a41bc5-e3ce-4691-b9e8-d0d435e0bcb8';
        $activities = $this->getObject()->readAll(null, null, array(), $contactId);

        $this->assertGreaterThan(0, $activities->count());
        foreach ($activities as $activity) {
            $this->assertEquals($activity->contactId, $contactId);
        }
    }

    /**
     * @covers Bronto_Api_Activity::readAll
     */
    public function testActivityContactIdFilterWithType()
    {
        $contactId  = '53a41bc5-e3ce-4691-b9e8-d0d435e0bcb8';
        $activities = $this->getObject()->readAll(null, null, array(Bronto_Api_Activity::TYPE_SEND), $contactId);

        $this->assertGreaterThan(0, $activities->count());
        foreach ($activities as $activity) {
            $this->assertEquals($activity->contactId, $contactId);
        }
    }

    /**
     * @return Bronto_Api_Contact
     */
    public function getContactObject()
    {
        return $this->getApi()->getContactObject();
    }

    /**
     * @return Bronto_Api_Activity
     */
    public function getObject()
    {
        return $this->getApi()->getActivityObject();
    }
}