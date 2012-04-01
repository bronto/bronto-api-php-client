<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @link http://community.bronto.com/api/v4/objects/general/accountobject
 */
class Bronto_Api_Account extends Bronto_Api_Object
{
    /** Status */
    const STATUS_UNRESTRICTED = 'unrestricted';
    const STATUS_RESTRICTED   = 'restricted';
    const STATUS_INACTIVE     = 'inactive';

    /**
     * @var array
     */
    protected $_methods = array(
        'addAccounts'    => 'add',
        'readAccounts'   => 'read',
        'updateAccounts' => 'update',
        'deleteAccounts' => 'delete',
    );

    /**
     * @var array
     */
    protected $_options = array(
        'status' => array(
            self::STATUS_UNRESTRICTED,
            self::STATUS_RESTRICTED,
            self::STATUS_INACTIVE,
        ),
    );

    /**
     * @param array $filter
     * @param bool $includeInfo
     * @param string $status
     * @param int $pageNumber
     * @return Bronto_Api_Rowset
     */
    public function readAll(array $filter = array(), $includeInfo = true, $status = null, $pageNumber = 1)
    {
        $params = array();
        $params['filter']      = $filter;
        $params['includeInfo'] = (bool) $includeInfo;
        if (!empty($status)) {
            $params['status'] = $status;
        }
        $params['pageNumber']  = (int) $pageNumber;
        return $this->read($params);
    }
}