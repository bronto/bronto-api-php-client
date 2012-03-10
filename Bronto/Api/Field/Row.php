<?php

/**
 * @property-read string $id
 * @property string $name
 * @property string $label
 * @property string $type
 * @property string $visibility
 * @property array $options
 * @method Bronto_Api_Field getApiObject() getApiObject()
 */
class Bronto_Api_Field_Row extends Bronto_Api_Row
{
    /**
     * @param bool $returnData
     * @return Bronto_Api_Field_Row
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
     * @param bool $refresh
     * @return Bronto_Api_Field_Row
     */
    public function save($upsert = true, $refresh = false)
    {
        if (!$upsert) {
            return parent::_save(false, $refresh);
        }

        try {
            return parent::_save(true, $refresh);
        } catch (Bronto_Api_Field_Exception $e) {
            if ($e->getCode() === Bronto_Api_Field_Exception::ALREADY_EXISTS) {
                $this->_refresh();
                return $this;
            }
            $this->getApiObject()->getApi()->throwException($e);
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        return parent::_delete(array('id' => $this->id));
    }
}