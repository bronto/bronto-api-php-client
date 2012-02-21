<?php

class Bronto_Api_Field_Exception extends Bronto_Api_Exception
{
    const INVALID_FIELD       = 401;
    const ALREADY_EXISTS	  = 402;
    const INVALID_DISPLAY	  = 403;
    const INVALID_NAME        = 404;
    const INVALID_VISIBILITY  = 405;
    const ALLOCATION_EXCEED	  = 408;
    const INVALID_FIELD_VALUE = 409;
    const DATA_TRUNCATION	  = 410;
    const SEGMENT_DEPENDENCY  = 411;
}