<?php

/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 * @property-read string $id
 * @property string $email
 * @property string $mobileNumber
 * @property string $status
 * @property string $msgPref
 * @property string $source
 * @property string $customSource
 * @property array $listIds
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
 * @method Bronto_Api_Contact_Row delete() delete()
 * @method Bronto_Api_Contact getApiObject() getApiObject()
 */
class Bronto_Api_Contact_Row extends Bronto_Api_Row implements Bronto_Api_Delivery_Recipient
{
    /**
     * @var array
     */
    protected $_data = array(
        'status'          => Bronto_Api_Contact::STATUS_TRANSACTIONAL,
        'messagePrefence' => Bronto_Api_Contact::MSGPREF_HTML,
        'source'          => Bronto_Api_Contact::SOURCE_API,
    );

    /**
     * Initialize object
     *
     * Called from {@link __construct()} as final step of object instantiation.
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
     * @return Bronto_Api_Contact_Row
     */
    public function read()
    {
        $params = array();
        if ($this->id) {
            $params = array('id' => $this->id);
        } elseif ($this->email) {
            $params = array(
                'email' => array(
                    'value'    => $this->email,
                    'operator' => 'EqualTo',
                )
            );
        } else {
            throw new Bronto_Api_Contact_Exception('Trying to read Contact without Id or Email for lookup');
        }

        parent::_read($params);
        return $this;
    }

    /**
     * @param bool $upsert
     * @param bool $refresh
     * @return Bronto_Api_Contact_Row
     */
    public function save($upsert = true, $refresh = false)
    {
        try {
            parent::_save($upsert, $refresh);
        } catch (Bronto_Api_Contact_Exception $e) {
            if ($e->getCode() === Bronto_Api_Contact_Exception::ALREADY_EXISTS) {
                $this->_refresh();
            } else {
                $e->appendToMessage("(Email: {$this->email})");
                $this->getApi()->throwException($e);
            }
        }

        return $this;
    }

    /**
     * @return Bronto_Api_Contact_Row
     */
    public function persist()
    {
        return parent::_persist('addOrUpdate', $this->email);
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
        if ($value === '') {
            return;
        }

        $fieldId = $field;
        if ($field instanceOf Bronto_Api_Field_Row) {
            if (!$field->id) {
                $field = $field->read();
            }
            $fieldId = $field->id;

            switch ($field->type) {
                case Bronto_Api_Field::TYPE_DATE:
                    if ($value instanceOf DateTime) {
                        $value = date('c', $value->getTimestamp());
                    } else {
                        $value = date('c', strtotime($value));
                    }
                    break;
                case Bronto_Api_Field::TYPE_INTEGER:
                    $value = (int) $value;
                    break;
                case Bronto_Api_Field::TYPE_FLOAT:
                    $value = (float) $value;
                    break;
            }
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
            try {
                if ($rowset = $this->getApiObject()->readAll(array('id' => $this->id), array($fieldId))) {
                    foreach ($rowset as $row) {
                        $data = $row->getData();
                        if (is_array($data) && !empty($data) && isset($data['fields'])) {
                            $this->_data['fields'] = array_merge(
                                isset($this->_data['fields']) ? $this->_data['fields'] : array(),
                                $data['fields']
                            );
                            $this->_cleanData = $this->_data;
                            break;
                        }
                    }
                }
            } catch (Exception $e) {
                return false;
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

    /**
     * @return array
     */
    public function getLists()
    {
        if ($this->id) {
            $filter = array('id' => $this->id);
        } else {
            $filter = array(
                'email' => array(
                    'value'    => $this->email,
                    'operator' => 'EqualTo',
                )
            );
        }

        try {
            $rowset = $this->getApiObject()->readAll($filter, array(), true);

            if ($rowset->count() > 0) {
                $data = $rowset->current()->getData();
                if (isset($data['listIds'])) {
                    return $data['listIds'];
                }
            }
        } catch (Exception $e) {
            // Ignore
        }

        return array();
    }

    /**
     * @param Bronto_Api_List_Row|string $list
     * @return Bronto_Api_Contact_Row
     */
    public function addToList($list)
    {
        $listId = $list;
        if ($list instanceOf Bronto_Api_List_Row) {
            if (!$list->id) {
                $list = $list->read();
            }
            $listId = $list->id;
        }

        if (!isset($this->_data['listIds'])) {
            $this->_loadLists();
        }

        if (!in_array($listId, $this->_data['listIds'])) {
            $this->_data['listIds'][] = $listId;
            $this->_modifiedFields['listIds'] = true;
        }
        return $this;
    }

    /**
     * @param Bronto_Api_List_Row|string $list
     * @return Bronto_Api_Contact_Row
     */
    public function removeFromList($list)
    {
        $listId = $list;
        if ($list instanceOf Bronto_Api_List_Row) {
            if (!$list->id) {
                $list = $list->read();
            }
            $listId = $list->id;
        }

        if (!isset($this->_data['listIds'])) {
            $this->_loadLists();
        }

        if (is_array($this->_data['listIds'])) {
            foreach ($this->_data['listIds'] as $i => $id) {
                if ($id == $listId) {
                    unset($this->_data['listIds'][$i]);
                    break;
                }
            }
        }

        $this->_modifiedFields['listIds'] = true;
        return $this;
    }

    /**
     * @return void
     */
    protected function _loadLists()
    {
        if (!isset($this->_data['listIds'])) {
            $this->_data['listIds'] = array();
        }

        $listIds = $this->getLists();
        foreach ($listIds as $listId) {
            $this->_data['listIds'][] = $listId;
            $this->_modifiedFields['listIds'] = true;
        }
    }

    /**
     * @param array $additionalFilter
     * @param int $pageNumber
     * @return Bronto_Api_Rowset
     */
    public function getDeliveries(array $additionalFilter = array(), $pageNumber = 1)
    {
        if (!$this->id) {
            $exceptionClass = $this->getExceptionClass();
            throw new $exceptionClass("This Contact has not been retrieved yet (has no ContactId)");
        }

        /* @var $deliveryObject Bronto_Api_Delivery */
        $deliveryObject = $this->getApi()->getDeliveryObject();
        $filter = array_merge_recursive(array('contactId' => $this->id), $additionalFilter);
        return $deliveryObject->readDeliveryRecipients($filter, $pageNumber);
    }

    /**
     * Set row field value
     *
     * @param  string $columnName The column key.
     * @param  mixed  $value      The value for the property.
     */
    public function __set($columnName, $value)
    {
        switch (strtolower($columnName)) {
            case 'email':
                // Trim whitespace
                $value = preg_replace('/\s+/', '', $value);
                // Check if email got truncated
                if (substr($value, -1) === '.') {
                    $value .= 'com';
                }
                break;
        }

        return parent::__set($columnName, $value);
    }

    /**
     * Required by Bronto_Api_Delivery_Recipient
     *
     * @return false
     */
    public function isList()
    {
        return false;
    }

    /**
     * Required by Bronto_Api_Delivery_Recipient
     *
     * @return true
     */
    public function isContact()
    {
        return true;
    }

    /**
     * Required by Bronto_Api_Delivery_Recipient
     *
     * @return false
     */
    public function isSegment()
    {
        return false;
    }
}
