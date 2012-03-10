<?php

class Bronto_Api_Segment extends Bronto_Api_Abstract
{
    /**
     * The object name.
     *
     * @var string
     */
    protected $_name = 'Segments';

    /**
     * @var string
     */
    protected $_rowClass = 'Bronto_Api_Segment_Row';

    /**
     * Classname for exceptions
     *
     * @var string
     */
    protected $_exceptionClass = 'Bronto_Api_Segment_Exception';

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

    /**
     * @param array $data
     * @throws Bronto_Api_Segment_Exception
     * @return void
     */
    public function createRow(array $data = array())
    {
        $exceptionClass = $this->getExceptionClass();
        throw new $exceptionClass('You cannot create a Segment row.');
    }
}