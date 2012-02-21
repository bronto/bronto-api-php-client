<?php

/**
 * @property-read string $id
 * @property-read string $name
 * @property-read array $rules
 * @property-read date $lastUpdated
 * @property-read float $activeCount
 * @method Bronto_Api_Segment getApiObject()
 */
class Bronto_Api_Segment_Row extends Bronto_Api_Row implements Bronto_Api_Delivery_Recipient
{
    /**
     * @var bool
     */
    protected $_readOnly = true;

    /**
     * @param bool $returnData
     * @return Bronto_Api_Contact_Row|array
     */
    public function read($returnData = false)
    {
        if ($this->id) {
            $params = array('id' => $this->id);
        } else {
            $params = array(
                'name' => array(
                    'value'    => $this->name,
                    'operator' => 'EqualTo',
                )
            );
        }

        return parent::_read($params, $returnData);
    }

    /**
     * Required by Bronto_Api_Delivery_Recipient
     *
     * @return false
     */
    public function isList()
    {
        return false;
    }

    /**
     * Required by Bronto_Api_Delivery_Recipient
     *
     * @return false
     */
    public function isContact()
    {
        return false;
    }

    /**
     * Required by Bronto_Api_Delivery_Recipient
     *
     * @return true
     */
    public function isSegment()
    {
        return true;
    }
}