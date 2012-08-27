<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @link http://community.bronto.com/api/v4/objects/general/orderobject
 *
 * @method Bronto_Api_Order_Row createRow() createRow(array $data = array())
 */
class Bronto_Api_Order extends Bronto_Api_Object
{
    /**
     * @var array
     */
    protected $_methods = array(
        'addOrUpdateOrders' => 'addOrUpdate',
        'deleteOrders'      => 'delete',
    );
}
