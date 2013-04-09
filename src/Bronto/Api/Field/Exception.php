<?php
/**
 * 
 * @copyright  2011-2013 Bronto Software, Inc.
 * @license http://opensource.org/licenses/OSL-3.0 Open Software License v. 3.0 (OSL-3.0)
 */
class Bronto_Api_Field_Exception extends Bronto_Api_Exception
{
    const INVALID_FIELD       = 401; // The specified field was invalid.
    const ALREADY_EXISTS	  = 402; // A field with this name already exists.
    const INVALID_DISPLAY	  = 403; // The specified field type was invalid:
    const INVALID_NAME        = 404; // The field name was missing or invalid.
    const INVALID_VISIBILITY  = 405; // The specified field visibility was invalid.
    const ALLOCATION_EXCEED	  = 408; // This operation would exceed your field allocation of %d.
    const INVALID_FIELD_VALUE = 409; // The value specified for the field %%id%% was invalid.
    const DATA_TRUNCATION	  = 410; // The value specified for the field %%id%% was too large.
    const SEGMENT_DEPENDENCY  = 411; // The field cannot be deleted because a segment depends upon it.
}
