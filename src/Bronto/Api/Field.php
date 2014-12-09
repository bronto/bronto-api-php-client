<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 * @link http://community.bronto.com/api/v4/objects/general/fieldobject
 *
 * @method Bronto_Api_Field_Row createRow() createRow(array $data = array())
 */
class Bronto_Api_Field extends Bronto_Api_Object
{
    /** Type */
    const TYPE_TEXT     = 'text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_PASSWORD = 'password';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_RADIO    = 'radio';
    const TYPE_SELECT   = 'select';
    const TYPE_INTEGER  = 'integer';
    const TYPE_CURRENCY = 'currency';
    const TYPE_FLOAT    = 'float';
    const TYPE_DATE     = 'date';

    /**
     * @var array
     */
    protected $_methods = array(
        'addFields'    => 'add',
        'readFields'   => 'read',
        'updateFields' => 'update',
        'deleteFields' => 'delete',
    );

    /**
     * @var array
     */
    protected $_options = array(
        'type' => array(
            self::TYPE_TEXT,
            self::TYPE_TEXTAREA,
            self::TYPE_PASSWORD,
            self::TYPE_CHECKBOX,
            self::TYPE_RADIO,
            self::TYPE_SELECT,
            self::TYPE_INTEGER,
            self::TYPE_CURRENCY,
            self::TYPE_FLOAT,
            self::TYPE_DATE,
        ),
    );

    /**
     * @var array
     */
    protected $_objectCache = array();

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

    /**
     * @param array $input_fields
     * @param array $all_fields
     * @return array
     */
    public function fieldsFromArray(array $input_fields,array $all_fields=null) {
        if ($all_fields===null) {
            $all_fields=$this->getAll();
        }
        $return_fields=array();
        foreach ($input_fields as $field_name => $content) {
            $return_fields[]=array(
                'fieldId'=>$all_fields['by_name'][$field_name],
                'content'=>$content,
            );
        }
        return $return_fields;
    }

    public function fieldNamesInErrors(array $errors) {
        foreach ($errors as &$error) {
            $message=&$error['message'];
            preg_match("|field '([a-f0-9]*)'|",$message,$field_id_search);
            if (!empty($field_id_search[1])) {
                $message=str_replace($field_id_search[1],$this->allFields['by_id'][$field_id_search[1]],$message);
            }
        }
        return $errors;
    }

    /**
     * @param string $index
     * @param Bronto_Api_Field_Row $field
     * @return Bronto_Api_Field
     */
    public function addToCache($index, Bronto_Api_Field_Row $field)
    {
        $this->_objectCache[$index] = $field;
        return $this;
    }

    /**
     * @param string $index
     * @return Bronto_Api_Field_Row
     */
    public function getFromCache($index)
    {
        if (isset($this->_objectCache[$index]) && $this->_objectCache[$index] instanceOf Bronto_Api_Field_Row) {
            return $this->_objectCache[$index];
        }
        return false;
    }

    /**
     * @param string $name
     * @return string
     */
    public function normalize($name)
    {
        $name = strtolower($name);
        $name = preg_replace("/[^a-z\d_]/i", '_', $name);
        $name = trim(preg_replace('/_+/', '_', $name), '_');

        return $name;
    }

    /**
     * @param string $name
     * @param array $values
     */
    public function guessType($name, array $values)
    {
        // Check predefined fields first
        if (isset(Bronto_Api_Field_Predefined::$normalizerMap[$name])) {
            if (isset(Bronto_Api_Field_Predefined::$predefinedFields[$name])) {
                return array(
                    $name => Bronto_Api_Field_Predefined::$predefinedFields[$name]
                );
            }
        } else {
            foreach (Bronto_Api_Field_Predefined::$normalizerMap as $key => $synonyms) {
                if (in_array($name, $synonyms)) {
                    return array(
                        $key => Bronto_Api_Field_Predefined::$predefinedFields[$key]
                    );
                }
            }
        }

        // Try to type guess
        $typeGuesser = new Bronto_Api_Field_TypeGuesser();
        $typeGuesser->processValues($values);
        return $typeGuesser->getChoice();
    }
}
