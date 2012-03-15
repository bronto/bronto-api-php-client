<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @link http://community.bronto.com/api/v4/objects/general/conversionobject
 *
 * @method Bronto_Api_Conversion_Row createRow() createRow(array $data = array())
 */
class Bronto_Api_Conversion extends Bronto_Api_Object
{
    /**
     * @var array
     */
    protected $_methods = array(
        'addConversion'   => 'add',
        'readConversions' => 'read',
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
}