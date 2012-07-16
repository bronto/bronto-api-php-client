<?php

/**
 * @group object
 * @group order
 */
class Bronto_Tests_Api_Order_OrderTest extends Bronto_Tests_AbstractTest
{
    protected $apiToken = TEST_API_TOKEN_1;

    public function testAddUpdateOrderUsingExitingContactEmail()
    {
        $orderObject = $this->getObject();

        /* @var $brontoOrder Bronto_Api_Order_Row */
        $brontoOrder = $orderObject->createRow();
        $brontoOrder->email       = 'leeked@gmail.com';
        $brontoOrder->id          = 1;
        $brontoOrder->createdDate = date('c', time());
        $brontoOrder->items       = array(
            array(
              'id'          => '1001',
              'sku'         => 'sku1',
              'description' => 'desc1',
              'quantity'    => 1,
              'amount'      => 1.00,
            ),
            array(
              'id'          => '1002',
              'sku'         => 'sku2',
              'description' => 'desc2',
              'quantity'    => 1,
              'amount'      => 2.00,
            )
        );

        try {
          $result = $brontoOrder->save();
          d($result);
        } catch (Exception $e) {
          echo $e->getMessage() . "\n";
          echo $orderObject->getApi()->getLastRequest() . "\n";
        }
    }

    public function testAddUpdateOrderUsingNewContactEmail()
    {
        $orderObject = $this->getObject();

        /* @var $brontoOrder Bronto_Api_Order_Row */
        $brontoOrder = $orderObject->createRow();
        $brontoOrder->email       = 'leeked+addupdateorder' . rand() . '@gmail.com';
        $brontoOrder->id          = 2;
        $brontoOrder->createdDate = date('c', time());
        $brontoOrder->items       = array(
            array(
              'id'          => '1001',
              'sku'         => 'sku1',
              'description' => 'desc1',
              'quantity'    => 1,
              'amount'      => 1.00,
            ),
            array(
              'id'          => '1002',
              'sku'         => 'sku2',
              'description' => 'desc2',
              'quantity'    => 1,
              'amount'      => 2.00,
            )
        );

        try {
          $result = $brontoOrder->save();
          d($result);
        } catch (Exception $e) {
          echo $e->getMessage() . "\n";
          echo $orderObject->getApi()->getLastRequest() . "\n";
        }
    }

    public function testAddUpdateOrderUsingContactId()
    {
        $orderObject = $this->getObject();

        /* @var $brontoOrder Bronto_Api_Order_Row */
        $brontoOrder = $orderObject->createRow();
        $brontoOrder->contactId   = '551dea25e-0b54-402a-af4c-c8947cf549a8';
        $brontoOrder->id          = 2;
        $brontoOrder->createdDate = date('c', time());
        $brontoOrder->items       = array(
            array(
              'id'          => '1001',
              'sku'         => 'sku1',
              'description' => 'desc1',
              'quantity'    => 1,
              'amount'      => 1.00,
            ),
            array(
              'id'          => '1002',
              'sku'         => 'sku2',
              'description' => 'desc2',
              'quantity'    => 1,
              'amount'      => 2.00,
            )
        );

        try {
          $result = $brontoOrder->save();
        } catch (Exception $e) {
          echo $e->getMessage() . "\n";
          echo $orderObject->getApi()->getLastRequest() . "\n";
        }
    }

    /**
     * @return Bronto_Api_Order
     */
    public function getObject()
    {
        return $this->getApi()->getOrderObject();
    }
}
