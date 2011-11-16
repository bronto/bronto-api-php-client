<?php

/**
 * @property-read string $id
 * @property string $email
 * @property string $status
 * @property string $msgPref
 * @property string $source
 * @property string $customSource
 * @property string $listIds
 * @property array $fields
 * @property-read string $created
 * @property-read string $modifed
 * @property-read bool $deleted
 * @property-read int $numSends
 * @property-read int $numBounces
 * @property-read int $numOpens
 * @property-read int $numClicks
 * @property-read int $numConversions
 * @property-read float $conversionAmount
 */
class Bronto_Api_Contact_Row extends Bronto_Api_Row
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
     * Initialize object
     *
     * Called from {@link __construct()} as final step of object instantiation.
     *
     * @return void
     */
    public function init()
    {
        if (isset($this->_data['fields']) && is_array($this->_data['fields'])) {
            foreach ($this->_data['fields'] as $i => $fieldRow) {
                $this->_data['fields'][$i] = (array) $fieldRow;
            }
            $this->_cleanData = $this->_data;
        }
    }

    /**
     * @param bool $returnData
     * @return Bronto_Api_Contact_Row|array
     */
    public function read($returnData = false)
    {
        if ($this->id) {
            $params = array('id' => $this->id);
        } else {
            $params = array(
                'email' => array(
                    'value'    => $this->email,
                    'operator' => 'EqualTo',
                )
            );
        }

        return parent::_read($params, $returnData);
    }

    /**
     * @param bool $upsert
     * @return Bronto_Api_Contact_Row
     */
    public function save($upsert = true)
    {
        if (!$upsert) {
            return parent::save();
        }

        try {
            return parent::save();
        } catch (Bronto_Api_Contact_Exception $e) {
            if ($e->getCode() == Bronto_Api_Contact_Exception::ALREADY_EXISTS) {
                $this->_refresh();
                return $this->id;
            }
            throw $e;
        }
    }

    /**
     * @return bool
     */
    public function delete()
    {
        return parent::_delete(array('id' => $this->id));
    }

    /**
     * Sets a value for a custom Field
     *
     * @param string|Bronto_Api_Field_Row $field
     * @param mixed $value
     * @return Bronto_Api_Contact_Row
     */
    public function setField($field, $value)
    {
        $fieldId = $field;
        if ($field instanceOf Bronto_Api_Field_Row) {
            if (!$field->id) {
                $field = $field->read();
            }
            $fieldId = $field->id;
        }

        $field = array(
            'fieldId' => $fieldId,
            'content' => $value,
        );

        if (!isset($this->_data['fields']) || !is_array($this->_data['fields'])) {
            $this->_data['fields'] = array();
        } else {
            // Check for dupes
            foreach ($this->_data['fields'] as $i => $_field) {
                if ($_field['fieldId'] == $field['fieldId']) {
                    $this->_data['fields'][$i] = $field;
                    $this->_modifiedFields['fields'] = true;
                    return $this;
                }
            }
        }

        $this->_data['fields'][] = $field;
        $this->_modifiedFields['fields'] = true;
        return $this;
    }

    /**
     * Retreives a value for a custom field
     * NOTE: Loads the field for you if it hasn't been requested
     *
     * @param string|Bronto_Api_Field_Row $field $field
     * @return mixed
     */
    public function getField($field)
    {
        $fieldId = $field;
        if ($field instanceOf Bronto_Api_Field_Row) {
            if (!$field->id) {
                $field = $field->read();
            }
            $fieldId = $field->id;
        }

        // Determine if we have the field already
        if (isset($this->_data['fields']) && is_array($this->_data['fields'])) {
            foreach ($this->_data['fields'] as $i => $fieldRow) {
                if ($fieldRow['fieldId'] == $fieldId) {
                    return $fieldRow['content'];
                }
            }
        }

        // We don't, so request it
        if ($this->id) {
            $params = array('id' => $this->id);
            $rowset = $this->getApiObject()->readAll($params, array($fieldId));
            if ($rowset->count() > 0) {
                $data = $rowset->current()->getData();
                $this->_data['fields'] = array_merge(
                    isset($this->_data['fields']) ? $this->_data['fields'] : array(),
                    $data['fields']
                );
                $this->_cleanData = $this->_data;
            }
        }

        // Try the traverse again
        if (isset($this->_data['fields']) && is_array($this->_data['fields'])) {
            foreach ($this->_data['fields'] as $i => $fieldRow) {
                if ($fieldRow['fieldId'] == $fieldId) {
                    return $fieldRow['content'];
                }
            }
        }

        // Something went horribly wrong
        return null;
    }

//    public function addToList($list)
//    {
//        $listId = $list;
//        if ($list instanceOf Bronto_Api_List_Row) {
//            if (!$list->id) {
//                $list = $list->read();
//            }
//            $listId = $list->id;
//        }
//    }

//    public function removeFromList($list)
//    {
//        $listId = $list;
//        if ($list instanceOf Bronto_Api_List_Row) {
//            if (!$list->id) {
//                $list = $list->read();
//            }
//            $listId = $list->id;
//        }
//    }

    /**
     * Proxy for intellisense
     *
     * @return Bronto_Api_Contact
     */
    public function getApiObject()
    {
        return parent::getApiObject();
    }
}