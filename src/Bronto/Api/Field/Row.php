<?php

/**
 * @copyright  2011-2015 Bronto Software, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * 
 * @property-read string $id
 * @property string $name
 * @property string $label
 * @property string $type
 * @property string $visibility
 * @property array $options
 * @method Bronto_Api_Field_Row delete() delete()
 * @method Bronto_Api_Field getApiObject() getApiObject()
 */
class Bronto_Api_Field_Row extends Bronto_Api_Row
{
    /**
     * @return Bronto_Api_Field_Row
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
     * @return Bronto_Api_Field_Row
     */
    public function save($upsert = true, $refresh = false)
    {
        if (!$upsert) {
            parent::_save(false, $refresh);
        }

        try {
            parent::_save(true, $refresh);
        } catch (Bronto_Api_Field_Exception $e) {
            if ($e->getCode() === Bronto_Api_Field_Exception::ALREADY_EXISTS) {
                $this->_refresh();
            } else {
                $e->appendToMessage("(Name: {$this->name})");
                $this->getApi()->throwException($e);
            }
        }

        return $this;
    }
}
