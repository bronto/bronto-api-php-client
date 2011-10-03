<?php

/**
 * @property-read string $id
 * @property string $name
 * @property string $label
 * @property int $activeCount
 * @property string $status
 * @property string $visibility
 */
class Bronto_Api_List_Row extends Bronto_Api_Row implements Bronto_Api_Delivery_Recipient
{
    /**
     * Retrieves contacts for current list
     * 
     * @param bool $includeLists
     * @param array $fields
     * @param int $pageNumber
     * @return Bronto_Api_Rowset
     */
    public function getContacts($includeLists = false, array $fields = array(), $pageNumber = 1)
    {
        $contactObject = $this->getApiObject()->getApi()->getContactObject();
        $filter = array(
            'listId' => $this->id,
        );
        return $contactObject->readAll($filter, $fields, $includeLists, $pageNumber);
    }
    
    /**
     * @param bool $returnData
     * @return Bronto_Api_List_Row|array
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
     * @param bool $upsert
     * @return Bronto_Api_List_Row
     */
    public function save($upsert = true)
    {
        if (!$upsert) {
            return parent::save();
        }
        
        try {
            return parent::save();
        } catch (Bronto_Api_List_Exception $e) {
            if ($e->getCode() == Bronto_Api_List_Exception::ALREADY_EXISTS) {
                $this->_refresh();
                return $this->id;
            }
            throw $e;
        }
    }
    
    /**
     * @return bool 
     */
    public function delete()
    {
        return parent::_delete(array('id' => $this->id));
    }
    
    /**
     * @return bool
     */
    public function clear()
    {
        return $this->getApiObject()->clear(array('id' => $this->id));
    }
    
    /**
     * Proxy for intellisense
     * 
     * @return Bronto_Api_List
     */
    public function getApiObject()
    {
        return parent::getApiObject();
    }
    
    /**
     * Required by Bronto_Api_Delivery_Recipient
     * 
     * @return bool
     */
    public function isList()
    {
        return true;
    }
}