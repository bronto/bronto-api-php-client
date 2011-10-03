<?php

/**
 * @property string $activityDate
 * @property string $contactId
 * @property string $deliveryId
 * @property string $messageId
 * @property string $listId
 * @property string $trackingType
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
 */
class Bronto_Api_Activity_Row extends Bronto_Api_Row
{   
    /** Type */
    const TYPE_OPEN        = 'open';
    const TYPE_CLICK       = 'click';
    const TYPE_CONVERSION  = 'conversion';
    const TYPE_BOUNCE      = 'bounce';
    const TYPE_SEND        = 'send';
    const TYPE_UNSUBSCRIBE = 'unsubscribe';
    const TYPE_VIEW        = 'view';
    
    /**
     * @var array
     */
    protected $_options = array(
        'trackingType' => array(
            self::TYPE_OPEN,
            self::TYPE_CLICK,
            self::TYPE_CONVERSION,
            self::TYPE_BOUNCE,
            self::TYPE_SEND,
            self::TYPE_UNSUBSCRIBE,
            self::TYPE_VIEW,
        ),
    );
    
    /**
     * @var bool
     */
    protected $_readOnly = true;
       
    /**
     * @param string $name
     * @param array $arguments
     * @return mixed 
     */
    public function __call($name, $arguments) {
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
                    $idField = "{$object}Id";
                    if (isset($this->{$idField}) && !empty($this->{$idField})) {
                        $apiObject = $this->getApiObject()->getApi()->getObject($object);
                        $row       = $apiObject->createRow();
                        $row->id   = $this->{$idField};
                        return $row->read();
                    } else {
                        return false;
                    }
                    break;

            }
        }
    }
    
    /**
     * Proxy for intellisense
     * 
     * @return Bronto_Api_Activity
     */
    public function getApiObject()
    {
        return parent::getApiObject();
    }
}