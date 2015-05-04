<?php

/**
 * 
 * @copyright  2011-2015 Bronto Software, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * 
 * @property-read string $id
 * @property string $name
 * @property string $status
 * @property string $messageFolderId
 * @property array $content
 * @method Bronto_Api_Message_Row delete() delete()
 * @method Bronto_Api_Message getApiObject() getApiObject()
 */
class Bronto_Api_Message_Row extends Bronto_Api_Row
{
    /**
     * @param Bronto_Api_DeliveryGroup_Row|string $deliveryGroup
     * @return bool
     */
    public function addToDeliveryGroup($deliveryGroup)
    {
        if (!$this->id) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass("This Message has not been saved yet (has no MessageId)");
        }

        $deliveryGroupId = $deliveryGroup;
        if ($deliveryGroup instanceOf Bronto_Api_DeliveryGroup_Row) {
            if (!$deliveryGroup->id) {
                $deliveryGroup = $deliveryGroup->read();
            }
            $deliveryGroupId = $deliveryGroup->id;
        }

        $deliveryGroupObject = $this->getApi()->getDeliveryGroupObject();
        return $deliveryGroupObject->addToDeliveryGroup($deliveryGroupId, array(), array($this->id));
    }

    /**
     * @return Bronto_Api_Message_Row
     */
    public function read()
    {
        if ($this->id) {
            $params = array('id' => $this->id);
        } elseif ($this->name) {
            $params = array(
                'name' => array(
                    'value'    => $this->name,
                    'operator' => 'EqualTo',
                )
            );
        }

        parent::_read($params);
        return $this;
    }

    /**
     * @param bool $upsert
     * @param bool $refresh
     * @return Bronto_Api_Message_Row
     */
    public function save($upsert = true, $refresh = false)
    {
        if (!$upsert) {
            parent::_save(false, $refresh);
        }

        try {
            parent::_save(true, $refresh);
        } catch (Bronto_Api_Message_Exception $e) {
            if ($e->getCode() === Bronto_Api_Message_Exception::MESSAGE_EXISTS) {
                $this->_refresh();
            } else {
                $this->getApi()->throwException($e);
            }
        }

        return $this;
    }
}
