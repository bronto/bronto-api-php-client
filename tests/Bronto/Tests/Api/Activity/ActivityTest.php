<?php

/**
 * @copyright  2011-2015 Bronto Software, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * 
 * @group object
 * @group activity
 */
class Bronto_Tests_Api_Activity_ActivityTest extends Bronto_Tests_AbstractTest
{
    protected $apiToken  = TEST_API_TOKEN_3;
    protected $contactId = TEST_CONTACT_ID_3;

    /**
     * @covers Bronto_Api_Activity::readAll
     * @covers Bronto_Api_Rowset::iterate
     */
    public function testActivityReadDirectionFilterWithIterator()
    {
        $activityObject  = $this->getObject();
        $currentActivity = 0;
        $currentPage     = 0;

        $iterator = $activityObject->readAll(null, null, Bronto_Api_Activity::TYPE_SEND)->iterate();
        foreach ($iterator as $activity /* @var $activity Bronto_Api_Activity_Row */) {
            $currentActivity++;
            if ($iterator->isNewPage()) {
                $currentPage++;
                $this->assertSame($currentPage, $iterator->getCurrentPage());
                if ($currentPage == 1) {
                    $this->assertEquals(Bronto_Api_Object::DIRECTION_FIRST, $iterator->getLastParamValue('readDirection'));
                } else {
                    $this->assertGreaterThan(1000, $currentActivity);
                    $this->assertEquals(0, ($currentActivity - 1) % 1000, '$currentActivity (' . ($currentActivity - 1) . ') is not 1000 divisible.');
                    $this->assertEquals(Bronto_Api_Object::DIRECTION_NEXT, $iterator->getLastParamValue('readDirection'), 'Current page (' . $currentPage . ') does not have NEXT as last filter.');
                }
            }

            if ($currentPage == 3) {
                $this->assertSame($currentActivity - 1, $iterator->getCurrentKey());
                break;
            }
        }

        $this->assertGreaterThan(2, $iterator->getCurrentPage());
        $this->assertGreaterThan(2, $currentPage);

        $this->assertGreaterThan(1001, $iterator->getCurrentKey());
        $this->assertGreaterThan(1001, $currentActivity);
    }

    /**
     * @covers Bronto_Api_Activity::readAll
     */
    public function testActivityReadDirectionFilter()
    {
        $activityObject = $this->getObject();

        $activities = $activityObject->readAll(null, null, Bronto_Api_Activity::TYPE_SEND);
        $this->assertGreaterThan(0, $activities->count());

        $firstActivity = false;
        foreach ($activities as $activity) {
            if ($firstActivity === false) {
                $firstActivity = $activity;
                break;
            }
        }

        $this->assertNotEquals(false, $firstActivity);

        if ($activities->count() < 1000) {
            $this->markTestIncomplete('Less activities were returned (' . $activities->count() . ') than the minimum page size (1000).');
        }

        $activities = $activityObject->readAll(null, null, Bronto_Api_Activity::TYPE_SEND, Bronto_Api_Activity::DIRECTION_NEXT);
        $this->assertGreaterThan(0, $activities->count());

        $nextActivity = false;
        foreach ($activities as $activity) {
            if ($nextActivity === false) {
                $nextActivity = $activity;
                break;
            }
        }

        $this->assertNotEquals(false, $nextActivity);

        // Check first activity result of first call against first result of second call
        $firstHash = md5(serialize($firstActivity->getData()));
        $nextHash  = md5(serialize($nextActivity->getData()));
        $this->assertNotSame($nextHash, $firstHash);
    }

    /**
     * @group disabled
     * @covers Bronto_Api_Activity::readAll
     */
    public function testActivityContactIdFilter()
    {
        // @todo Remove if contactIds filter is re-enabled
        $this->markTestSkipped('The contactIds filter is currently disabled.');

        $activities = $this->getObject()->readAll(null, null, null, null, $this->contactId);
        $this->assertGreaterThan(0, $activities->count());

        foreach ($activities as $activity) {
            $this->assertEquals($activity->contactId, $this->contactId);
        }
    }

    /**
     * @group disabled
     * @covers Bronto_Api_Activity::readAll
     */
    public function testActivityContactIdFilterWithEmptyType()
    {
        // @todo Remove if contactIds filter is re-enabled
        $this->markTestSkipped('The contactIds filter is currently disabled.');

        $activities = $this->getObject()->readAll(null, null, array(), null, $this->contactId);
        $this->assertGreaterThan(0, $activities->count());

        foreach ($activities as $activity) {
            $this->assertEquals($activity->contactId, $this->contactId);
        }
    }

    /**
     * @group disabled
     * @covers Bronto_Api_Activity::readAll
     */
    public function testActivityContactIdFilterWithType()
    {
        // @todo Remove if contactIds filter is re-enabled
        $this->markTestSkipped('The contactIds filter is currently disabled.');

        $activities = $this->getObject()->readAll(null, null, array(Bronto_Api_Activity::TYPE_SEND), null, $this->contactId);
        $this->assertGreaterThan(0, $activities->count());

        foreach ($activities as $activity) {
            $this->assertEquals($activity->contactId, $this->contactId);
        }
    }

    /**
     * @return Bronto_Api_Activity
     */
    public function getObject()
    {
        return $this->getApi()->getActivityObject();
    }
}
