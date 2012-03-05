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
 * @property date createdDate
 * @property string deliveryId
 * @property string messageId
 * @property string automatorId
 * @property string listId
 * @property string segmentId
 * @property string deliveryType
 * @method Bronto_Api_Conversion getApiObject()
 */
class Bronto_Api_Conversion_Row extends Bronto_Api_Row
{
    /**
     * @param bool $returnData
     * @return Bronto_Api_Conversion_Row|array
     */
    public function read($returnData = false)
    {
        $params = array();
        if ($this->id) {
            $params['id'] = $this->id;
        } elseif ($this->contactId) {
            $params['contactId'] = $this->contactId;
        } elseif ($this->deliveryId) {
            $params['deliveryId'] = $this->deliveryId;
        } elseif ($this->orderId) {
            $params['orderId'] = $this->orderId;
        }

        return parent::_read($params, $returnData);
    }

    /**
     * @param bool $upsert
     * @param bool $refresh
     * @return Bronto_Api_Conversion_Row
     */
    public function save($upsert = false, $refresh = true)
    {
        /**
         * If the _cleanData array is empty,
         * this is an ADD of a new row.
         * Otherwise it is an UPDATE.
         */
        if (empty($this->_cleanData)) {
            return parent::save(false, $refresh);
        } else {
            require_once 'Bronto/Api/Row/Exception.php';
            throw new Bronto_Api_Row_Exception(sprintf("Cannot update a %s record.", $this->getApiObject()->getName()));
        }
    }

    /**
     * @return void
     */
    public function delete()
    {
        require_once 'Bronto/Api/Row/Exception.php';
        throw new Bronto_Api_Row_Exception(sprintf("Cannot delete a %s record.", $this->getApiObject()->getName()));
    }
}