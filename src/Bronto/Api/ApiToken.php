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

    /** Permissions Shortcuts */
    const PERMISSIONS_READ_WRITE      = 3;
    const PERMISSIONS_READ_SEND       = 5;
    const PERMISSIONS_WRITE_SEND      = 6;
    const PERMISSIONS_READ_WRITE_SEND = 7;

    /**
     * @var array
     */
    protected $_options = array(
        'permissions' => array(
            self::PERMISSION_READ,
            self::PERMISSION_WRITE,
            self::PERMISSION_SEND,
            self::PERMISSIONS_READ_WRITE,
            self::PERMISSIONS_READ_SEND,
            self::PERMISSIONS_WRITE_SEND,
            self::PERMISSIONS_READ_WRITE_SEND,
        ),
    );

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