<?php

/**
 * @group object
 * @group order
 */
class Bronto_Tests_Api_Order_OrderTest extends Bronto_Tests_AbstractTest
{
    protected $apiToken  = TEST_API_TOKEN_2;

    public function testAddUpdateOrder()
    {
        $orderObject = $this->getObject();

        /* @var $brontoOrder Bronto_Api_Order_Row */
        $brontoOrder = $orderObject->createRow();
        $brontoOrder->email       = 'leeked@gmail.com';
        $brontoOrder->orderId     = 1;
        $brontoOrder->createdDate = date('c', time());
        $brontoOrder->items       = array(
            array(
               'sku'         => 'sku1',
               'description' => 'desc1',
               'quantity'    => 1,
               'amount'      => 1.00,
            ),
            array(
               'sku'         => 'sku2',
               'description' => 'desc2',
               'quantity'    => 1,
               'amount'      => 2.00,
            )
        );
        $result = $brontoOrder->save();

        var_dump($result); exit;
    }

    /**
     * @return Bronto_Api_Order
     */
    public function getObject()
    {
        return $this->getApi()->getOrderObject();
    }
}
