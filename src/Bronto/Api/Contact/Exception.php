<?php
/**
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 */
class Bronto_Api_Contact_Exception extends Bronto_Api_Exception
{
    const INVALID_REQUEST              = 301; // Invalid request:
    const NOT_FOUND                    = 302; // Contact does not exist.
    const INVALID_EMAIL                = 303; // Invalid email address:
    const INVALID_STATUS               = 304; // Invalid status:
    const ALREADY_EXISTS               = 305; // Contact already exists:
    const INVALID_FIELD                = 306; // Invalid field attributes.
    const MAX_SEARCH_ITEMS_EXCEEDED    = 311; // The maximum number of contact search items was exceeded.
    const MAX_SEARCH_LISTS_EXCEEDED    = 312; // The maximum number of contact search lists was exceeded.
    const MAX_SEARCH_SEGMENTS_EXCEEDED = 313; // The maximum number of contact search segments was exceeded.
    const EMAIL_ALREADY_EXISTS         = 314; // Email address already exists on another contact.
    const EMAIL_SUPPRESSED             = 315; // Email address is on suppression list.
    const INVALID_EMAIL_LENGTH         = 317; // Email address cannot exceed 100 characters in length:
    const MOBILE_ALREADY_EXISTS        = 318; // Mobile number already exists on another contact.
    const INVALID_MOBILE               = 319; // Invalid mobile number: %s
    const MISSING_EMAIL_AND_MOBILE     = 320; // Email address or mobile number is required.
}
