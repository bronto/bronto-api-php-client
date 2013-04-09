<?php

/**
 *
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 * @property-read string $id
 * @property string $contactId
 * @property string $email
 * @property array $products
 * @property string $orderDate
 * @property string $deliveryId
 * @property string $messageId
 * @property string $automatorId
 * @property string $listId
 * @property string $segmentId
 * @property string $deliveryType
 * @property-write string $tid
 * @method Bronto_Api_Order_Row delete() delete()
 * @method Bronto_Api_Order getApiObject() getApiObject()
 */
class Bronto_Api_Order_Row extends Bronto_Api_Row
{
    /**
     * @param bool $upsert Ignored
     * @param bool $refresh
     * @return Bronto_Api_Order_Row
     */
    public function save($upsert = true, $refresh = false)
    {
        parent::_add(true);
        return $this;
    }

    /**
     * @return Bronto_Api_Order_Row
     */
    public function persist()
    {
        return parent::_persist('addOrUpdate', false);
    }

    /**
     * Set row field value
     *
     * @param  string $columnName The column key.
     * @param  mixed  $value      The value for the property.
     */
    public function __set($columnName, $value)
    {
        switch (strtolower($columnName)) {
            case 'email':
                // Trim whitespace
                $value = preg_replace('/\s+/', '', $value);
                // Check if email got truncated
                if (substr($value, -1) === '.') {
                    $value .= 'com';
                }
                break;
        }

        return parent::__set($columnName, $value);
    }

    /**
     * @param array $data
     * @return Bronto_Api_Order_Product
     */
    public function addProduct(array $data = array())
    {
        $product   = new Bronto_Api_Order_Product($data);
        $productId = $product->id;

        if (empty($productId)) {
            throw new Bronto_Api_Order_Exception('Product must have a value for ID.');
        }

        if (isset($this->products[$productId])) {
            throw new Bronto_Api_Order_Exception("Product already exists in Order with ID: {$productId}");
        }

        $this->products[$productId] = $product;
        return $product;
    }
}
