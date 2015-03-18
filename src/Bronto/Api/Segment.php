<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @link http://community.bronto.com/api/v4/objects/general/segmentobject
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Bronto_Api_Segment extends Bronto_Api_Object
{
    /**
     * @var array
     */
    protected $_methods = array(
        'readSegments' => 'read',
    );

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
     */
    public function createRow(array $data = array())
    {
        throw new Bronto_Api_Segment_Exception('You cannot create a Segment row.');
    }
}
