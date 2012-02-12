<?php

/** @var Bronto_Api_Activity_Row */
require_once 'Bronto/Api/Activity/Row.php';

/** @var Bronto_Api_Activity_Exception */
require_once 'Bronto/Api/Activity/Exception.php';

class Bronto_Api_Activity extends Bronto_Api_Abstract
{
    /**
     * The object name.
     *
     * @var string
     */
    protected $_name = 'Activities';
    
    /**
     * The primary key column or columns.
     *
     * @var mixed
     */
    protected $_primary = array('activityDate', 'trackingType');
    
    /**
     * @var string
     */
    protected $_rowClass = 'Bronto_Api_Activity_Row';
    
    /**
     * Classname for exceptions
     *
     * @var string
     */
    protected $_exceptionClass = 'Bronto_Api_Activity_Exception';
    
    /**
     * @param string $startDate
     * @param int $size
     * @param array $types
     * @throws Bronto_Api_Activity_Exception
     * @return Bronto_Api_Rowset 
     */
    public function readAll($startDate, $size = 25, array $types = array())
    {
        $filter = array();
        $filter['start'] = $startDate;
        $filter['size']  = (int) $size;
        if (!empty($types)) {
            $filter['types'] = $types;
        }
        return parent::readAll(array('filter' => $filter));
    }
    
    /**
     * @throws Bronto_Api_Activity_Exception
     * @return void
     */
    public function createRow()
    {
        $exceptionClass = $this->getExceptionClass();
        throw new $exceptionClass('You cannot create an Activity row.');
    }
}