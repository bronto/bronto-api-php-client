<?php
/**
 * @property-read id
 * @property name
 * @property type
 * @property messagedId
 */

//content type, subject, content
class Bronto_Api_Messagerule_Row extends Bronto_Api_Row
{   
    
    /**
     * @var bool
     */
    protected $_isNew = false;

       /**
     * @return Bronto_Api_Message_Row
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


    /**
     * @return Bronto_Api_Message
     */
    public function getApiObject()
    {
        return parent::getApiObject();
    }
}