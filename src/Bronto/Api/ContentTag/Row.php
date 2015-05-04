<?php

/**
 * 
 * @copyright  2011-2015 Bronto Software, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * 
 * @property-read string $id
 * @property string $name
 * @property string value
 * @method Bronto_Api_ContentTag_Row delete() delete()
 * @method Bronto_Api_ContentTag getApiObject() getApiObject()
 */
class Bronto_Api_ContentTag_Row extends Bronto_Api_Row
{
    /**
     * @return Bronto_Api_ContentTag_Row
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
     * @return Bronto_Api_ContentTag_Row
     */
    public function save($upsert = true, $refresh = false)
    {
        if (!$upsert) {
            parent::_save(false, $refresh);
        }

        try {
            parent::_save(true, $refresh);
        } catch (Bronto_Api_ContentTag_Exception $e) {
            if ($e->getCode() === Bronto_Api_ContentTag_Exception::MESSAGE_EXISTS) {
                $this->_refresh();
            } else {
                $this->getApi()->throwException($e);
            }
        }

        return $this;
    }
}