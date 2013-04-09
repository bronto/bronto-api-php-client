<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 */
class Bronto_Api_Field_Predefined
{
    /** Predefined Fields */
    const FIELD_EMAIL          = 'email';
    const FIELD_SALUTATION     = 'salutation';
    const FIELD_FIRSTNAME      = 'firstname';
    const FIELD_LASTNAME       = 'lastname';
    const FIELD_AGE            = 'age';
    const FIELD_GENDER         = 'gender';
    const FIELD_BIRTHDAY       = 'birthday';
    const FIELD_ADDRESS1       = 'address1';
    const FIELD_ADDRESS2       = 'address2';
    const FIELD_CITY           = 'city';
    const FIELD_STATE          = 'state';
    const FIELD_STATE_ABBR     = 'state_abbrev';
    const FIELD_STATE_ABBR_UC  = 'state_abbrev_uc';
    const FIELD_STATE_PROVINCE = 'state_province';
    const FIELD_POSTAL_CODE    = 'postal_code';
    const FIELD_COUNTRY        = 'country';
    const FIELD_COUNTRY_CODE   = 'country_code';
    const FIELD_PHONE_HOME     = 'phone_home';
    const FIELD_PHONE_WORK     = 'phone_work';
    const FIELD_PHONE_MOBILE   = 'phone_mobile';
    const FIELD_NUM_CHILDREN   = 'num_children';
    const FIELD_EDUCATION      = 'education';
    const FIELD_MARITAL_STATUS = 'marital_status';

    /**
     * @var array
     */
    public static $predefinedFields = array(
        self::FIELD_SALUTATION => array(
            'label' => 'Salutation',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_FIRSTNAME => array(
            'label' => 'First Name',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_LASTNAME => array(
            'label' => 'Last Name',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_AGE => array(
            'label' => 'Age',
            'type'  => Bronto_Api_Field::TYPE_INTEGER,
        ),
        self::FIELD_GENDER => array(
            'label'   => 'Gender',
            'type'    => Bronto_Api_Field::TYPE_RADIO,
            'options' => array(
                array(
                    'value'     => 'male',
                    'isDefault' => false,
                ),
                array(
                    'value'     => 'female',
                    'isDefault' => false,
                ),
            ),
        ),
        self::FIELD_BIRTHDAY => array(
            'label' => 'Birthday',
            'type'  => Bronto_Api_Field::TYPE_DATE,
        ),
        self::FIELD_ADDRESS1 => array(
            'label' => 'Address',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_ADDRESS2 => array(
            'label' => 'Address (Contd.)',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_CITY => array(
            'label' => 'City',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_STATE => array(
            'label' => 'State',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_STATE_ABBR => array(
            'label' => 'State (Two-Letter Abbreviation)',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_STATE_ABBR_UC => array(
            'label' => 'State (Uppercase Two-Letter Abbreviation)',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_STATE_PROVINCE => array(
            'label' => 'State or Province',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_POSTAL_CODE => array(
            'label' => 'Postal/ZIP Code',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_COUNTRY => array(
            'label' => 'Country',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_COUNTRY_CODE => array(
            'label' => 'Country',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_PHONE_HOME => array(
            'label' => 'Home Phone',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_PHONE_WORK => array(
            'label' => 'Work Phone',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_PHONE_MOBILE => array(
            'label' => 'Mobile / Cell Phone',
            'type'  => Bronto_Api_Field::TYPE_TEXT,
        ),
        self::FIELD_NUM_CHILDREN => array(
            'label'   => 'Number of Children',
            'type'    => Bronto_Api_Field::TYPE_SELECT,
            'options' => array(
                array(
                    'value'     => 'None (0)',
                    'isDefault' => true,
                ),
                array(
                    'value'     => '1',
                    'isDefault' => false,
                ),
                array(
                    'value'     => '2',
                    'isDefault' => false,
                ),
                array(
                    'value'     => '3',
                    'isDefault' => false,
                ),
                array(
                    'value'     => '4',
                    'isDefault' => false,
                ),
                array(
                    'value'     => '5',
                    'isDefault' => false,
                ),
                array(
                    'value'     => '6',
                    'isDefault' => false,
                ),
                array(
                    'value'     => '7',
                    'isDefault' => false,
                ),
                array(
                    'value'     => '8',
                    'isDefault' => false,
                ),
                array(
                    'value'     => '9',
                    'isDefault' => false,
                ),
                array(
                    'value'     => '10',
                    'isDefault' => false,
                ),
            ),
        ),
        self::FIELD_EDUCATION => array(
            'label'   => 'Highest Level of Education',
            'type'    => Bronto_Api_Field::TYPE_SELECT,
            'options' => array(
                array(
                    'value'     => 'somehighschool',
                    'isDefault' => false,
                ),
                array(
                    'value'     => 'highschool',
                    'isDefault' => false,
                ),
                array(
                    'value'     => 'somecollege',
                    'isDefault' => false,
                ),
                array(
                    'value'     => 'college',
                    'isDefault' => false,
                ),
                array(
                    'value'     => 'somegraduate',
                    'isDefault' => false,
                ),
                array(
                    'value'     => 'graduate',
                    'isDefault' => false,
                ),
            ),
        ),
        self::FIELD_MARITAL_STATUS => array(
            'label'   => 'Marital Status',
            'type'    => Bronto_Api_Field::TYPE_SELECT,
            'options' => array(
                array(
                    'value'     => 'single',
                    'isDefault' => false,
                ),
                array(
                    'value'     => 'married',
                    'isDefault' => false,
                ),
                array(
                    'value'     => 'divorced',
                    'isDefault' => false,
                ),
                array(
                    'value'     => 'widowed',
                    'isDefault' => false,
                ),
            ),
        ),
    );

    /**
     * @var array
     */
    public static $normalizerMap = array(
        self::FIELD_EMAIL => array(
            'email_address',
            'emailaddress',
        ),
        self::FIELD_SALUTATION => array(),
        self::FIELD_FIRSTNAME => array(
            'first_name',
            'fname',
        ),
        self::FIELD_LASTNAME => array(
            'last_name',
            'lname',
        ),
        self::FIELD_AGE => array(),
        self::FIELD_GENDER => array(),
        self::FIELD_BIRTHDAY => array(
            'birth_day',
            'birth_date',
            'dob',
            'birthdate',
            'dateofbirth',
            'date_of_birth',
        ),
        self::FIELD_ADDRESS1 => array(
            'address_line1',
            'addressline1',
            'address_1',
        ),
        self::FIELD_ADDRESS2 => array(
            'address_line2',
            'addressline2',
            'address_2',
        ),
        self::FIELD_CITY => array(
            'cityname',
            'city_name',
        ),
        self::FIELD_STATE => array(),
        self::FIELD_STATE_ABBR => array(),
        self::FIELD_STATE_ABBR_UC => array(),
        self::FIELD_STATE_PROVINCE => array(),
        self::FIELD_POSTAL_CODE => array(
            'zipcode',
            'zip',
            'postcode',
            'postalcode',
            'zip_code',
        ),
        self::FIELD_COUNTRY => array(),
        self::FIELD_COUNTRY_CODE => array(),
        self::FIELD_PHONE_HOME => array(
            'phonehome',
            'home_phone',
            'phone',
            'homephone',
        ),
        self::FIELD_PHONE_WORK => array(
            'phonework',
            'work_phone',
            'workphone',
        ),
        self::FIELD_PHONE_MOBILE => array(
            'mobile',
            'mobile_phone',
            'cell',
            'cellphone',
            'mobilephone',
        ),
        self::FIELD_NUM_CHILDREN => array(),
        self::FIELD_EDUCATION => array(),
        self::FIELD_MARITAL_STATUS => array(),
    );
}
