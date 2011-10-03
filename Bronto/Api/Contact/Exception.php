<?php

class Bronto_Api_Contact_Exception extends Bronto_Api_Exception
{
    const INVALID_REQUEST              = 301;
    const NOT_FOUND                    = 302;
    const INVALID_EMAIL                = 303;
    const INVALID_STATUS               = 304;
    const ALREADY_EXISTS               = 305;
    const INVALID_FIELD                = 306;
    const MAX_SEARCH_ITEMS_EXCEEDED    = 311;
    const MAX_SEARCH_LISTS_EXCEEDED    = 312;
    const MAX_SEARCH_SEGMENTS_EXCEEDED = 313;
    const EMAIL_ALREADY_EXISTS         = 314;
    const EMAIL_SUPPRESSED             = 315;
    const INVALID_EMAIL_LENGTH         = 317;
}