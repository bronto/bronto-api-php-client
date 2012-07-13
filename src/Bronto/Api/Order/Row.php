<?php

/**
 * @property-read string $id
 * @property string $contactId
 * @property string $email
 * @property string $orderId
 * @property array $items
 * @property string $createdDate
 * @property string $deliveryId
 * @property string $messageId
 * @property string $automatorId
 * @property string $listId
 * @property string $segmentId
 * @property string $deliveryType
 * @property-write string $tid
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
}
