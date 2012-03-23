<?php

/**
 * @group retryer
 */
class Bronto_Tests_Api_Retryer_FileRetryerTest extends Bronto_Tests_AbstractTest
{
    /**
     * @var Bronto_Api_Retryer_FileRetryer
     */
    public $retryer;

    public function setUp()
    {
        $tempPath = realpath(TESTS_BASEPATH . '/../../') . DIRECTORY_SEPARATOR . 'temp';
        $this->retryer = $this->getApi()->getRetryer(array('type' => 'file', 'path' => $tempPath));
    }

    /**
     * @covers Bronto_Api_Retryer_FileRetryer::store
     */
    public function testFileRetryerStore()
    {
        // Create dummy object
        $contactObject = $this->getApi()->getContactObject();
        try {
            $contact = array('email' => 'example+onevalid' . time() . '@bronto.com');
            $contactObject->addOrUpdate($contact);

            // Throw some recoverable error
            throw new Bronto_Api_Exception('The API is currently undergoing maintenance', Bronto_Api_Exception::SHARD_OFFLINE);
        } catch (\Exception $e) {
            if ($e->isRecoverable()) {
                $filename = $this->retryer->store($contactObject);
            }
        }

        $filePath = $this->retryer->getPath($filename);
        $this->assertTrue(file_exists($filePath));

        return $filePath;
    }

    /**
     * @covers Bronto_Api_Retryer_FileRetryer::attempt
     * @depends testFileRetryerStore
     */
    public function testFileRetryerAttempt($filePath)
    {
        $rowset = $this->retryer->attempt($filePath);

        $this->assertTrue($rowset instanceOf Bronto_Api_Rowset);
        $this->assertGreaterThan(0, $rowset->count());
    }
}