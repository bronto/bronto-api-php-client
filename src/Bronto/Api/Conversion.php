<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
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
        $params = array(
            'filter'     => array(),
            'pageNumber' => (int) $pageNumber,
        );

        if (!empty($filter)) {
            if (is_array($filter)) {
                $params['filter'] = $filter;
            } else {
                $params['filter'] = array($filter);
            }
        } else {
            $params['filter'] = array('contactId' => array());
        }

        return parent::read($params);
    }
}
