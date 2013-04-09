<?php
/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 */
class Bronto_Api_Activity_Exception extends Bronto_Api_Exception
{
    const INVALID_START_DATE    = 1201; // Start date is invalid:
    const INVALID_ACTIVITY_TYPE = 1202; // Invalid Activity types:
    const INVALID_SIZE          = 1203; // Activity size is invalid:
    const NO_CONTACT_FILTER     = 1204; // Activities cannot currently be filtered by contact ID
}
