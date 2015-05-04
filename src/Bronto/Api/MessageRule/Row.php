<?php

/**
 * 
 * @copyright  2011-2015 Bronto Software, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * 
 * @property-read id
 * @property name
 * @property type
 * @property messagedId
 * @method Bronto_Api_MessageRule_Row delete() delete()
 * @method Bronto_Api_MessageRule getApiObject() getApiObject()
 */
class Bronto_Api_MessageRule_Row extends Bronto_Api_Row
{
    /**
     * @param bool $returnData
     * @return Bronto_Api_MessageRule_Row
     */
    public function read()
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
     * @param bool $refresh
     * @return Bronto_Api_MessageRule_Row
     */
    public function save($upsert = true, $refresh = false)
    {
        if (!$upsert) {
            parent::_save(false, $refresh);
        }

        try {
            parent::_save(true, $refresh);
        } catch (Bronto_Api_MessageRule_Exception $e) {
            if ($e->getCode() === Bronto_Api_MessageRule_Exception::AUTOMATOR_EXISTS) {
                $this->_refresh();
            } else {
                $this->getApi()->throwException($e);
            }
        }

        return $this;
    }
}
