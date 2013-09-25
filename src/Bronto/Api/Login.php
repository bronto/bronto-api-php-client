<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 * @link http://community.bronto.com/api/v4/objects/general/accountobject
 *
 * @method Bronto_Api_Login_Row createRow() createRow(array $data)
 */
class Bronto_Api_Login extends Bronto_Api_Object
{
    /**
     * @var array
     */
    protected $_methods = array(
        'addLogins'    => 'add',
        'readLogins'   => 'read',
        'updateLogins' => 'update',
        'deleteLogins' => 'delete',
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

        return parent::read($params);
    }
}
