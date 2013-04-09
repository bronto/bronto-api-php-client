<?php
/**
 * 
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 */
class Bronto_Api_MessageRule_Exception extends Bronto_Api_Exception
{
    const INVALID_AUTOMATOR        = 604; // The specified automator is invalid.
    const INVALID_AUTOMATOR_NAME   = 610; // The message rule name is invalid.
    const INVALID_AUTOMATOR_TYPE   = 611; // The message rule type is invalid.
    const INVALID_AUTOMATOR_STATUS = 612; // The message rule status is invalid.
    const AUTOMATOR_EXISTS         = 613; // A message rule with this name already exists: %s
}
