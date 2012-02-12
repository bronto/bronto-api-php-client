<?php

/**
 * @property-read id
 * @property name
 * @property type
 * @property messagedId
 * @method Bronto_Api_Messagerule getApiObject()
 */
class Bronto_Api_Messagerule_Row extends Bronto_Api_Row
{
    /**
     * @return Bronto_Api_Messagerule_Row
     */
    public function read()
    {
        $filter = array('id' => $this->id);
        return parent::_read('MessageRules', $filter);
    }

    /**
     * @return bool
     */
    public function delete()
    {
        return parent::_delete(array('id' => $this->id));
    }
}