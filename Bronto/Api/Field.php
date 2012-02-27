<?php

/** @var Bronto_Api_Field_Row */
require_once 'Bronto/Api/Field/Row.php';

/** @var Bronto_Api_Field_Exception */
require_once 'Bronto/Api/Field/Exception.php';

/**
 * @method Bronto_Api_Field_Row createRow() createRow(array $data = array())
 */
class Bronto_Api_Field extends Bronto_Api_Abstract
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
     * The object name.
     *
     * @var string
     */
    protected $_name = 'Fields';

    /**
     * @var string
     */
    protected $_rowClass = 'Bronto_Api_Field_Row';

    /**
     * Classname for exceptions
     *
     * @var string
     */
    protected $_exceptionClass = 'Bronto_Api_Field_Exception';

    /**
     * @param array $filter
     * @param int $pageNumber
     * @return Bronto_Api_Rowset
     */
    public function readAll(array $filter = array(), $pageNumber = 1)
    {
        $params = array();
        $params['filter']     = $filter;
        $params['pageNumber'] = (int) $pageNumber;
        return $this->read($params);
    }
}