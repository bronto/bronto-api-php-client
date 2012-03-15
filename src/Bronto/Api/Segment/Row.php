<?php

/**
 * @property-read string $id
 * @property-read string $name
 * @property-read array $rules
 * @property-read string $lastUpdated
 * @property-read float $activeCount
 * @method Bronto_Api_Segment getApiObject() getApiObject()
 */
class Bronto_Api_Segment_Row extends Bronto_Api_Row implements Bronto_Api_Delivery_Recipient
{
    /**
     * @var bool
     */
    protected $_readOnly = true;

    /**
     * @return Bronto_Api_Segment_Row
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