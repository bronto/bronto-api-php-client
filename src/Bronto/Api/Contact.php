<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
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
    public function readAll($filter = array(), $fields = array(), $includeLists = true, $pageNumber = 1)
    {
        $params = array(
            'filter'       => array(),
            'fields'       => array(),
            'includeLists' => (bool) $includeLists,
            'pageNumber'   => (int) $pageNumber,
        );

        if (!empty($filter)) {
            if (is_array($filter)) {
                $params['filter'] = $filter;
            } else {
                $params['filter'] = array($filter);
            }
        }

        if (!empty($fields)) {
            if (is_array($fields)) {
                $params['fields'] = $fields;
            } else {
                $params['fields'] = array($fields);
            }
        }

        return parent::read($params);
    }
}
