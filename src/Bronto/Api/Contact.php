<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @link http://community.bronto.com/api/v4/objects/general/contactobject
 *
 * @method Bronto_Api_Contact_Row createRow() createRow(array $data)
 */
class Bronto_Api_Contact extends Bronto_Api_Object
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
    protected $_methods = array(
        'addContacts'           => 'add',
        'readContacts'          => 'read',
        'updateContacts'        => 'update',
        'deleteContacts'        => 'delete',
        'addOrUpdateContacts'   => 'addOrUpdate',
        'addToList'             => true,
        'removeFromList'        => true,
        'addContactsToWorkflow' => true,
        'addContactEvent'       => true,
    );

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
        return parent::read($params);
    }
}