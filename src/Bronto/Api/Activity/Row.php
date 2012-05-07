<?php

/**
 * @property-read string $activityDate
 * @property-read string $contactId
 * @property-read string $deliveryId
 * @property-read string $messageId
 * @property-read string $listId
 * @property-read string $trackingType
 * @method bool isOpen() isOpen()
 * @method bool isClick() isClick()
 * @method bool isConversion() isConversion()
 * @method bool isBounce() isBounce()
 * @method bool isSend() isSend()
 * @method bool isUnsubscribe() isUnsubscribe()
 * @method bool isView() isView()
 * @method Bronto_Api_Contact_Row getContact() getContact()
 * @method Bronto_Api_Delivery_Row getDelivery() getDelivery()
 * @method Bronto_Api_Message_Row getMessage() getMessage()
 * @method Bronto_Api_List_Row getList() getList()
 * @method Bronto_Api_Activity getApiObject() getApiObject()
 */
class Bronto_Api_Activity_Row extends Bronto_Api_Row
{
    /**
     * Tracks columns that are dates.
     *
     * @var array
     */
    protected $_dateFields = array(
        'activityDate' => true
    );

    /**
     * @var bool
     */
    protected $_readOnly = true;

    /**
     * @param string $name
     * @param array $arguments
     * @return Bronto_Api_Row
     */
    public function __call($name, $arguments)
    {
        // Check is{Type}
        if (substr($name, 0, 2) == 'is') {
            $type = strtolower(substr($name, 2));
            if ($this->getApiObject()->isValidOptionValue('trackingType', $type)) {
                return $this->trackingType == $type;
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
                        $apiObject = $this->getApi()->getObject($object);
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

        throw new BadMethodCallException("The method {$name} does not exist");
    }

    /**
     * @param mixed $upsert
     * @param mixed $refresh
     * @throws Bronto_Api_Activity_Exception
     */
    public function save($upsert = null, $refresh = null)
    {
        throw new Bronto_Api_Activity_Exception('You cannot create/update an Activity row.');
    }

    /**
     * @throws Bronto_Api_Activity_Exception
     */
    public function delete()
    {
        throw new Bronto_Api_Activity_Exception('You cannot delete an Activity row.');
    }
}