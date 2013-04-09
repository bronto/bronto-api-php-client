<?php
/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 */
class Bronto_Api_Delivery_Exception extends Bronto_Api_Exception
{
    const INVALID_SEND_DATE                  = 201; // The send date is invalid.
    const INVALID_FROM_ADDRESS               = 202; // The from address is invalid.
    const FATAL_ERROR_SEND                   = 203; // Fatal error sending delivery.
    const INVALID_RECIPIENT_TYPE             = 204; // The recipient type is invalid.
    const INVALID_MESSAGE                    = 205; // The delivery message was not found:
    const INVALID_LIST                       = 206; // The list for this delivery was not found:
    const INVALID_SEGMENT                    = 207; // The segment for this delivery was not found:
    const INVALID_SUBSCRIBER                 = 208; // The subscriber for this delivery was not found:
    const NO_RECIPIENTS                      = 209; // Your delivery has no recipients.
    const INVALID_FROM_NAME                  = 213; // The from name is invalid.
    const MESSAGE_NOT_TRANSACTIONAL_APPROVED = 215; // Message not approved for transactional sending:
    const MESSAGE_FIELD_MISSING_POSITION     = 216; // Missing required position in message field name: %s
    const NONUNIQUE_MESSAGE_FIELD_POSITION   = 217; // Position must be unique in message field name: %s
    const NOT_FOUND                          = 219; // Delivery does not exist: %s
    const INVALID_THROTTLE                   = 220; // Throttle rate must be in range [0, 720] (minutes)
}
