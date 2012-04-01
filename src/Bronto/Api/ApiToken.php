<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @link http://community.bronto.com/api/v4/objects/general/apitokenobject
 *
 * @method Bronto_Api_ApiToken_Row createRow() createRow(array $data)
 */
class Bronto_Api_ApiToken extends Bronto_Api_Object
{
    /** Permissions */
    const PERMISSION_READ  = 1;
    const PERMISSION_WRITE = 2;
    const PERMISSION_SEND  = 4;

    /**
     * @var array
     */
    protected $_methods = array(
        'addApiTokens'    => 'add',
        'readApiTokens'   => 'read',
        'updateApiTokens' => 'update',
        'deleteApiTokens' => 'delete',
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