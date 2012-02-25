<?php

/** @var Bronto_Api_Message_Row */
require_once 'Bronto/Api/Message/Row.php';

/** @var Bronto_Api_Message_Exception */
require_once 'Bronto/Api/Message/Exception.php';

/**
 * @method Bronto_Api_Message_Row createRow() createRow(array $data = array())
 */
class Bronto_Api_Message extends Bronto_Api_Abstract
{
    /**
     * The object name.
     *
     * @var string
     */
    protected $_name = 'Messages';

    /**
     * @var string
     */
    protected $_rowClass = 'Bronto_Api_Message_Row';

    /**
     * Classname for exceptions
     *
     * @var string
     */
    protected $_exceptionClass = 'Bronto_Api_Message_Exception';

    /**
     * @param array $filter
     * @param bool $includeContent
     * @param int $pageNumber
     * @return Bronto_Api_Rowset
     */
    public function readAll(array $filter = array(), $includeContent = false, $pageNumber = 1)
    {
        $params = array();
        $params['filter']         = $filter;
        $params['includeContent'] = (bool) $includeContent;
        $params['pageNumber']     = (int)  $pageNumber;
        return $this->read($params);
    }
}