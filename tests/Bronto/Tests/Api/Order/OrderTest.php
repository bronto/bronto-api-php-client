<?php

/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * 
 * @group object
 * @group order
 */
class Bronto_Tests_Api_Order_OrderTest extends Bronto_Tests_AbstractTest
{
    protected $apiToken = TEST_API_TOKEN_1;

    /**
     * @covers Bronto_Api_Order::flush()
     */
    public function testPersistCapturesErrors()
    {
        $orderObject = $this->getObject();

        /* @var $order1 Bronto_Api_Order_Row */
        $order1 = $orderObject->createRow();
        $order1->email       = 'leeked+testpersist' . time() . '@gmail.com';
        $order1->id          = rand();
        $order1->orderDate   = date('c', time());
        $order1->products    = array(
            array(
              'id'          => '1001',
              'sku'         => 'sku1',
              'description' => 'desc1',
              'quantity'    => 1,
              'price'       => 1.00,
            ),
            array(
              'id'          => '1002',
              'sku'         => 'sku2',
              'description' => 'desc2',
              'quantity'    => 1,
              'price'       => 2.00,
            )
        );
        $order1->persist();

        /* @var $order2 Bronto_Api_Order_Row */
        $order2 = $orderObject->createRow();
        $order2->email       = 'leeked+testpersist' . time() . '@gmail.com';
        $order2->id          = rand();
        $order2->tid         = rand();
        $order2->orderDate   = date('c', time());
        $order2->products    = array(
            array(
              'id'          => '1001',
              'sku'         => 'sku1',
              'description' => 'desc1',
              'quantity'    => 1,
              'price'       => 1.00,
            ),
            array(
              'id'          => '1002',
              'sku'         => 'sku2',
              'description' => 'desc2',
              'quantity'    => 1,
              'price'       => 2.00,
            )
        );
        $order2->persist();

        $result = $orderObject->flush();

        $this->assertTrue($result->hasErrors());
        $this->assertTrue(is_array($result->getErrors()));
        $this->assertTrue(count($result->getErrors()) === 1);
    }

    /**
     * @covers Bronto_Api_Order_Row::save()
     */
    public function testAddUpdateOrderUsingExitingContactEmail()
    {
        $orderObject = $this->getObject();

        /* @var $order Bronto_Api_Order_Row */
        $order = $orderObject->createRow();
        $order->email       = 'leeked@gmail.com';
        $order->id          = rand();
        $order->orderDate   = date('c', time());
        $order->items       = array(
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
        $result = $order->save();

        $this->assertTrue($order instanceOf Bronto_Api_Order_Row);
        $this->assertSame(spl_object_hash($order), spl_object_hash($result));
        $this->assertNotEmpty($order->id);
    }

    /**
     * @covers Bronto_Api_Order_Row::save()
     */
    public function testAddUpdateOrderUsingNewContactEmail()
    {
        $orderObject = $this->getObject();

        /* @var $order Bronto_Api_Order_Row */
        $order = $orderObject->createRow();
        $order->email       = 'leeked+addupdateorder' . rand() . '@gmail.com';
        $order->id          = rand();
        $order->orderDate   = date('c', time());
        $order->items       = array(
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
        $result = $order->save();

        $this->assertTrue($order instanceOf Bronto_Api_Order_Row);
        $this->assertSame(spl_object_hash($order), spl_object_hash($result));
        $this->assertNotEmpty($order->id);
    }

    /**
     * @covers Bronto_Api_Order_Row::save()
     */
    public function testAddUpdateOrderUsingContactId()
    {
        $orderObject = $this->getObject();

        /* @var $order Bronto_Api_Order_Row */
        $order = $orderObject->createRow();
        $order->contactId   = '551dea25e-0b54-402a-af4c-c8947cf549a8';
        $order->id          = rand();
        $order->orderDate   = date('c', time());
        $order->items       = array(
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

        $result = $order->save();

        $this->assertTrue($order instanceOf Bronto_Api_Order_Row);
        $this->assertSame(spl_object_hash($order), spl_object_hash($result));
        $this->assertNotEmpty($order->id);
    }

    /**
     * @return Bronto_Api_Order
     */
    public function getObject()
    {
        return $this->getApi()->getOrderObject();
    }
}
