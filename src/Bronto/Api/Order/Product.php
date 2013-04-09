<?php

/**
 * @author Chris Jones <chris.jones@bronto.com>
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 * 
 */
class Bronto_Api_Order_Product
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $sku;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $category;

    /**
     * @var string
     */
    public $image;

    /**
     * @var string
     */
    public $url;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var float
     */
    public $price;

    /**
     * @param array $data
     */
    public function __construct($data = array())
    {
        $data = (array) $data;
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }
}
