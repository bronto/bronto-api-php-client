<?php

/** @var Bronto_Api_Field_Row */
require_once 'Bronto/Api/Field/Row.php';

/** @var Bronto_Api_Field_Exception */
require_once 'Bronto/Api/Field/Exception.php';

class Bronto_Api_Field extends Bronto_Api_Abstract
{
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
        return parent::readAll($params);
    }
    
    /**
     * @param array $data
     * @return Bronto_Api_Field_Row
     */
    public function createRow(array $data = array())
    {
        return parent::createRow($data);
    }
}