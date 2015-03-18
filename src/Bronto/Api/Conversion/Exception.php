<?php
/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://www.apache.org/licenses/LICENSE-2.0
 */
class Bronto_Api_Conversion_Exception extends Bronto_Api_Exception
{
    const DUPLICATE_ORDER  = 901;	// Duplicate Order Id: %%id%%.
    const MISSING_AMOUNT   = 902;	// Missing required field: amount.
    const MISSING_QUANTITY = 903;	// Missing required field: quantity.
}
