<?php

namespace Bronto\Tests\Api\Activity;

use Bronto\Tests\AbstractTest;

/**
 * @group object
 * @group activity
 */
class ActivityTest extends AbstractTest
{
    public function testActivityFilterWorks()
    {
        $activities = $this->getObject()->readAll();
        dd($activities);
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