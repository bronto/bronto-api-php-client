<?php

/**
 * @property-read string $id
 * @property string $name
 * @property string $label
 * @property string $type
 * @property string $visibility
 * @property array $options
 * @method Bronto_Api_Field getApiObject()
 */
class Bronto_Api_Field_Row extends Bronto_Api_Row
{
    /** Type */
    const TYPE_TEXT     = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_PASSWORD = 'password';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RADIO    = 'radio';
    const TYPE_SELECT   = 'select';
    const TYPE_INTEGER  = 'integer';
    const TYPE_CURRENCY = 'currency';
    const TYPE_FLOAT    = 'float';
    const TYPE_DATE     = 'date';

    /**
     * @var array
     */
    protected $_options = array(
        'type' => array(
            self::TYPE_TEXT,
            self::TYPE_TEXTAREA,
            self::TYPE_PASSWORD,
            self::TYPE_CHECKBOX,
            self::TYPE_RADIO,
            self::TYPE_SELECT,
            self::TYPE_INTEGER,
            self::TYPE_CURRENCY,
            self::TYPE_FLOAT,
            self::TYPE_DATE,
        ),
    );

    /**
     * @param bool $returnData
     * @return Bronto_Api_Field_Row|array
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
     * @return Bronto_Api_List_Row
     */
    public function save($upsert = true, $refresh = true)
    {
        if (!$upsert) {
            return parent::save($upsert, $refresh);
        }

        try {
            return parent::save($upsert, $refresh);
        } catch (Bronto_Api_Field_Exception $e) {
            if ($e->getCode() == Bronto_Api_Field_Exception::ALREADY_EXISTS) {
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
}