<?php

/**
 * @property-read string id
 * @property string contactId
 * @property string email
 * @property string orderId
 * @property string item
 * @property string description
 * @property int quantity
 * @property float amount
 * @property float orderTotal
 * @property string createdDate
 * @property string deliveryId
 * @property string messageId
 * @property string automatorId
 * @property string listId
 * @property string segmentId
 * @property string deliveryType
 * @method Bronto_Api_Conversion getApiObject() getApiObject()
 */
class Bronto_Api_Conversion_Row extends Bronto_Api_Row
{
    /**
     * @param bool $returnData
     * @return Bronto_Api_Conversion_Row
     */
    public function read($returnData = false)
    {
        $params = array();

        if ($this->id) {
            $params['id'] = array($this->id);
        }

        if ($this->contactId) {
            $params['contactId'] = array($this->contactId);
        }

        if ($this->deliveryId) {
            $params['deliveryId'] = array($this->deliveryId);
        }

        if ($this->orderId) {
            $params['orderId'] = array($this->orderId);
        }

        return parent::_read($params, $returnData);
    }

    /**
     * @param bool $refresh
     * @return Bronto_Api_Conversion_Row
     * @throws Bronto_Api_Row_Exception
     */
    public function save($refresh = false)
    {
        /**
         * If the _cleanData array is empty,
         * this is an ADD of a new row.
         * Otherwise it is an UPDATE.
         */
        if (empty($this->_cleanData)) {
            return parent::_save(false, $refresh);
        } else {
            throw new Bronto_Api_Row_Exception(sprintf("Cannot update a %s record.", $this->getApiObject()->getName()));
        }
    }

    /**
     * @return void
     * @throws Bronto_Api_Row_Exception
     */
    public function delete()
    {
        throw new Bronto_Api_Row_Exception(sprintf("Cannot delete a %s record.", $this->getApiObject()->getName()));
    }
}