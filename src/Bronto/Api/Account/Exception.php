<?php
/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 */
class Bronto_Api_Account_Exception extends Bronto_Api_Exception
{
    const INVALID_SITE       = 701; // The account is invalid.
    const DUPLICATE_SITE     = 702; // There is already an account with the name: %s
    const INVALID_TOKEN      = 703; // The API token was invalid.
    const INVALID_TOKEN_SITE = 704; // The account specified for the token was invalid: %s
    const INVALID_TOKEN_NAME = 705; // The name specified for the token was invalid: %s
}
