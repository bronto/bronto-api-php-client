<?php

class Bronto_Api_Delivery_Exception extends Bronto_Api_Exception
{
    const INVALID_SEND_DATE      = 201;	// The send date is invalid.
    const INVALID_FROM_ADDRESS   = 202;	// The from address is invalid.
    const FATAL_ERROR_SEND       = 203;	// Fatal error sending delivery.
    const INVALID_RECIPIENT_TYPE = 204;	// The recipient type is invalid.
    const INVALID_MESSAGE        = 205;	// The delivery message was not found:
    const INVALID_LIST           = 206; // The list for this delivery was not found:
    const INVALID_SEGMENT        = 207;	// The segment for this delivery was not found:
    const INVALID_SUBSCRIBER     = 208;	// The subscriber for this delivery was not found:
    const NO_RECIPIENTS          = 209;	// Your delivery has no recipients.
    const INVALID_FROM_NAME      = 213;	// The from name is invalid.
}