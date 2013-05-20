<?php
/**
 * 
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 */
class Bronto_Api_Message_Exception extends Bronto_Api_Exception
{
    const INVALID_CONTENTTAG        = 1601; // The content tag specified is invalid.
    const MISSING_NAME              = 1602; // You must specify a name.
    const NAME_TOO_LONG             = 1603; // Name must be 100 characters or less.
    const INVALID_VALUE             = 1604; // Tag value cannot contain another content tag.
    const ALREADY_EXISTS            = 1605; // A content tag with this name already exists.
}
