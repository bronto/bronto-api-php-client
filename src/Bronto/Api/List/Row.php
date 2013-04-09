<?php

/**
 * 
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 * @property-read string $id
 * @property string $name
 * @property string $label
 * @property int $activeCount
 * @property string $status
 * @property string $visibility
 * @method Bronto_Api_List_Row delete() delete()
 * @method Bronto_Api_List getApiObject() getApiObject()
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
        $contactObject = $this->getApi()->getContactObject();
        $filter = array('listId' => $this->id);
        return $contactObject->readAll($filter, $fields, $includeLists, $pageNumber);
    }

    /**
     * @return Bronto_Api_List_Row
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
     * @return Bronto_Api_List_Row
     */
    public function save($upsert = true, $refresh = false)
    {
        if (!$upsert) {
            parent::_save(false, $refresh);
        }

        try {
            parent::_save(true, $refresh);
        } catch (Bronto_Api_List_Exception $e) {
            if ($e->getCode() === Bronto_Api_List_Exception::ALREADY_EXISTS) {
                $this->_refresh();
            } else {
                $this->getApi()->throwException($e);
            }
        }

        return $this;
    }

    /**
     * @return Bronto_Api_List_Row
     */
    public function clear()
    {
        $data = array();
        if (!$this->id) {
            $this->_refresh();
        }

        if ($this->id) {
            $data = array('id' => $this->id);
        } else {
            $exceptionClass = $this->getApiObject()->getExceptionClass();
            throw new $exceptionClass('Nothing to clear.');
        }

        $this->getApiObject()->clear($data);
        return $this;
    }

    /**
     * Required by Bronto_Api_Delivery_Recipient
     *
     * @return true
     */
    public function isList()
    {
        return true;
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
     * @return false
     */
    public function isSegment()
    {
        return false;
    }
}
