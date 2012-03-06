<?php

/** @var Bronto_Api_Contact_Row */
require_once 'Bronto/Api/Contact/Row.php';

/** @var Bronto_Api_Contact_Exception */
require_once 'Bronto/Api/Contact/Exception.php';

/**
 * @method Bronto_Api_Contact_Row createRow() createRow(array $data)
 */
class Bronto_Api_Contact extends Bronto_Api_Abstract
{
    /** Status */
    const STATUS_ACTIVE        = 'active';
    const STATUS_ONBOARDING    = 'onboarding';
    const STATUS_TRANSACTIONAL = 'transactional';
    const STATUS_BOUNCE        = 'bounce';
    const STATUS_UNCONFIRMED   = 'unconfirmed';
    const STATUS_UNSUBSCRIBED  = 'unsub';

    /** MsgPref */
    const MSGPREF_TEXT = 'text';
    const MSGPREF_HTML = 'html';

    /** Source */
    const SOURCE_MANUAL     = 'manual';
    const SOURCE_IMPORT     = 'import';
    const SOURCE_API        = 'api';
    const SOURCE_WEBFORM    = 'webform';
    const SOURCE_SALESFORCE = 'sforcereport';

    /**
     * @var array
     */
    protected $_options = array(
        'msgPref' => array(
            self::MSGPREF_TEXT,
            self::MSGPREF_HTML,
        ),
        'status' => array(
            self::STATUS_ACTIVE,
            self::STATUS_ONBOARDING,
            self::STATUS_TRANSACTIONAL,
            self::STATUS_BOUNCE,
            self::STATUS_UNCONFIRMED,
            self::STATUS_UNSUBSCRIBED,
        ),
        'source' => array(
            self::SOURCE_MANUAL,
            self::SOURCE_IMPORT,
            self::SOURCE_API,
            self::SOURCE_WEBFORM,
            self::SOURCE_SALESFORCE,
        ),
    );

    /**
     * The object name.
     *
     * @var string
     */
    protected $_name = 'Contacts';

    /**
     * Whether or not this object has an addOrUpdate method
     *
     * @var bool
     */
    protected $_hasUpsert = true;

    /**
     * @var string
     */
    protected $_rowClass = 'Bronto_Api_Contact_Row';

    /**
     * Classname for exceptions
     *
     * @var string
     */
    protected $_exceptionClass = 'Bronto_Api_Contact_Exception';

    /**
     * @param array $filter
     * @param array $fields
     * @param bool $includeLists
     * @param int $pageNumber
     * @return Bronto_Api_Rowset
     */
    public function readAll(array $filter = array(), array $fields = array(), $includeLists = true, $pageNumber = 1)
    {
        $params = array();
        $params['filter']       = $filter;
        $params['fields']       = $fields;
        $params['includeLists'] = (bool) $includeLists;
        $params['pageNumber']   = (int)  $pageNumber;
        return $this->read($params);
    }
}