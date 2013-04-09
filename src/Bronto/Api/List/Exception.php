<?php
/**
 * 
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 */
class Bronto_Api_List_Exception extends Bronto_Api_Exception
{
    const INVALID_LIST          = 501;
    const ALREADY_EXISTS        = 502;
    const LIST_IS_SEGMENTED     = 503;
    const LIST_HAS_AUTOMATORS	= 504;
    const LIST_HAS_DELIVERIES	= 505;
    const ALREADY_ON_LIST       = 506;
    const MAX_CONTACTS_EXCEEDED	= 507;
    const NO_CONTACTS_SPECIFIED	= 508;
    const LABEL_LENGTH_EXCEEDED	= 509;
    const NAME_LENGTH_EXCEEDED	= 510;
}
