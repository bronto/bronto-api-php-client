<?php

/** @var Bronto_Api_Contact_Row */
require_once 'Bronto/Api/Contact/Row.php';

/** @var Bronto_Api_Contact_Exception */
require_once 'Bronto/Api/Contact/Exception.php';

class Bronto_Api_Contact extends Bronto_Api_Abstract
{
    /**
     * The object name.
     *
     * @var string
     */
    protected $_name = 'Contacts';

    /**
     * @var string
     */
    protected $_rowClass = 'Bronto_Api_Contact_Row';

    /**
     * Classname for exceptions
     *
     * @var string
     */
    protected $_exceptionClass = 'Bronto_Api_Contact_Exception';

    /**
     * Temporarily set to false
     * 
     * @var bool
     */
    protected $_hasUpsert = false;

    /**
     * @param array $filter
     * @param array $fields
     * @param bool $includeLists
     * @param int $pageNumber
     * @return Bronto_Api_Rowset
     */
    public function readAll(array $filter = array(), array $fields = array(), $includeLists = false, $pageNumber = 1)
    {
        $params = array();
        $params['filter']       = $filter;
        $params['fields']       = $fields;
        $params['includeLists'] = (bool) $includeLists;
        $params['pageNumber']   = (int)  $pageNumber;
        return parent::readAll($params);
    }

    /**
     * @param array $data
     * @return Bronto_Api_Contact_Row
     */
    public function createRow(array $data = array())
    {
        return parent::createRow($data);
    }
}