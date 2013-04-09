<?php
/**
 * 
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 */
class Bronto_Api_Message_Exception extends Bronto_Api_Exception
{
    const INVALID_FOLDER_ID        = 601; // The folder id is invalid.
    const INVALID_FOLDER_NAME      = 602; // The folder name is invalid:
    const INVALID_MESSAGEGROUP     = 603; // The specified message is invalid.
    const INVALID_AUTOMATOR        = 604; // The specified automator is invalid.
    const INVALID_SOURCE_TEMPLATE  = 605; // Invalid message from template:
    const INVALID_CONTENT          = 606; // You must specify message content
    const INVALID_TYPE             = 607; // Message content type must be either 'text' or 'html'.
    const INVALID_SUBJECT          = 608; // You must specify a message subject.
    const INVALID_DYNAMIC_CONTENT  = 609; // The message's dynamic content is invalid.
    const INVALID_AUTOMATOR_NAME   = 610; // The message rule name is invalid.
    const INVALID_AUTOMATOR_TYPE   = 611; // The message rule type is invalid.
    const INVALID_AUTOMATOR_STATUS = 612; // The message rule status is invalid.
    const AUTOMATOR_EXISTS         = 613; // A message rule with this name already exists:
    const FOLDER_EXISTS            = 614; // A folder with this name already exists:
    const MESSAGE_EXISTS           = 615; // A message with this name already exists:
}
