<?php

/** @var Bronto_Api_Message_Row */
require_once 'Bronto/Api/Messagerule/Row.php';

/** @var Bronto_Api_Message_Exception */
require_once 'Bronto/Api/Messagerule/Exception.php';

/**
 * @method Bronto_Api_Messagerule_Row createRow() createRow(array $data = array())
 */
class Bronto_Api_Messagerule extends Bronto_Api_Abstract
{
    /**
     * The object name.
     *
     * @var string
     */
    protected $_name = 'MessageRules';

    /**
     * @var string
     */
    protected $_rowClass = 'Bronto_Api_Messagerule_Row';

    /**
     * Classname for exceptions
     *
     * @var string
     */
    protected $_exceptionClass = 'Bronto_Api_Messagerule_Exception';

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
     * @return Bronto_Api_Message_Row
     */
    public function delete(array $data = array())
    {
        return parent::delete('Messages', $data);
    }
}