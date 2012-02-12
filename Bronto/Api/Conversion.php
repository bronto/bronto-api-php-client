<?php

/** @var Bronto_Api_Conversion_Row */
require_once 'Bronto/Api/Conversion/Row.php';

/** @var Bronto_Api_Conversion_Exception */
require_once 'Bronto/Api/Conversion/Exception.php';

/**
 * @method Bronto_Api_Conversion_Row createRow() createRow(array $data = array())
 */
class Bronto_Api_Conversion extends Bronto_Api_Abstract
{
    /**
     * The object name.
     *
     * @var string
     */
    protected $_name = 'Conversions';

    /**
     * @var string
     */
    protected $_rowClass = 'Bronto_Api_Conversion_Row';

    /**
     * Classname for exceptions
     *
     * @var string
     */
    protected $_exceptionClass = 'Bronto_Api_Conversion_Exception';

    /**
     * @param array $filter
     * @param int $pageNumber
     * @throws Bronto_Api_Conversion_Exception
     * @return Bronto_Api_Rowset
     */
    public function readAll(array $filter = array(), $pageNumber = 1)
    {
        $params = array();
        $params['filter']     = $filter;
        $params['pageNumber'] = (int) $pageNumber;
        return parent::readAll($params);
    }
}