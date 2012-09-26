<?php

abstract class Bronto_Tests_AbstractTest extends PHPUnit_Framework_TestCase
{
    protected $apiToken  = TEST_API_TOKEN_3;
    protected $contactId = TEST_CONTACT_ID_1;

    /**
     * Performs assertions shared by all tests of a test case.
     *
     * This method is called before the execution of a test starts
     * and after setUp() is called.
     */
    protected function assertPreConditions()
    {
        try {
            $api = $this->getApi();
            $api->login();
        } catch (Bronto_Api_Exception $e) {
            if ($e->getCode() === Bronto_Api_Exception::SHARD_OFFLINE) {
                $this->markTestSkipped('Cannot run test suite while API is undergoing maintenance.');
                return;
            }

            throw $e;
        }
    }

    /**
     * @return Bronto_Api
     */
    public function getApi()
    {
        return new Bronto_Api($this->apiToken);
    }
}
