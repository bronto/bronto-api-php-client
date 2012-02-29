<?php

/**
 * @property-read string $activityDate
 * @property-read string $contactId
 * @property-read string $deliveryId
 * @property-read string $messageId
 * @property-read string $listId
 * @property-read string $trackingType
 * @method bool isOpen()
 * @method bool isClick()
 * @method bool isConversion()
 * @method bool isBounce()
 * @method bool isSend()
 * @method bool isUnsubscribe()
 * @method bool isView()
 * @method Bronto_Api_Contact_Row getContact()
 * @method Bronto_Api_Delivery_Row getDelivery()
 * @method Bronto_Api_Message_Row getMessage()
 * @method Bronto_Api_List_Row getList()
 * @method Bronto_Api_Activity getApiObject()
 */
class Bronto_Api_Activity_Row extends Bronto_Api_Row
{
    /**
     * @var bool
     */
    protected $_readOnly = true;

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        // Check is{Type}
        if (substr($name, 0, 2) == 'is') {
            $type = strtolower(substr($name, 2));
            if (in_array($type, $this->_options['trackingType'])) {
                return $this->trackingType == strtoupper($type);
            }
        }

        // Check get{Object}
        if (substr($name, 0, 3) == 'get') {
            $object = strtolower(substr($name, 3));
            switch ($object) {
                case 'contact':
                case 'delivery':
                case 'message':
                case 'list':
                    // Cache object result
                    $cacheObject = (bool) (isset($arguments[0]) && $arguments[0]);
                    $idField     = "{$object}Id";
                    if (isset($this->{$idField}) && !empty($this->{$idField})) {
                        if ($cacheObject) {
                            $cached = $this->getApiObject()->getFromCache($object, $this->{$idField});
                            if ($cached) {
                                return $cached;
                            }
                        }
                        $apiObject = $this->getApiObject()->getApi()->getObject($object);
                        $row       = $apiObject->createRow();
                        $row->id   = $this->{$idField};
                        $row->read();
                        if ($cacheObject) {
                            $this->getApiObject()->addToCache($object, $this->{$idField}, $row);
                        }
                        return $row;
                    } else {
                        return false;
                    }
                    break;
            }
        }
    }
}