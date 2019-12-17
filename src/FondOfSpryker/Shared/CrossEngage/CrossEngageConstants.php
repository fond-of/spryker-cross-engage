<?php

namespace FondOfSpryker\Shared\CrossEngage;

interface CrossEngageConstants
{
    public const CROSS_ENGAGE_URL = 'CROSS_ENGAGE_URL';
    public const CROSS_ENGAGE_API_KEY = 'CROSS_ENGAGE_API_KEY';
    public const CROSS_ENGAGE_API_HEADER = 'CROSS_ENGAGE_API_HEADER';
    public const CROSS_ENGAGE_API_URI_EVENTS = 'CROSS_ENGAGE_API_URI_EVENTS';
    public const CROSS_ENGAGE_API_URI = 'CROSS_ENGAGE_API_URI';
    public const CROSS_ENGAGE_API_URI_FETCH_USER = 'CROSS_ENGAGE_API_URI_FETCH_USER';

    public const XNG_HEADER_FIELD_CONTENT_TYPE = 'Content-Type';
    public const XNG_HEADER_FIELD_API_VERSION = 'X-XNG-ApiVersion';
    public const XNG_HEADER_FIELD_AUTH_TOKEN = 'X-XNG-AuthToken';

    public const XNG_STATE_NULL = 'null';
    public const XNG_STATE_NEW = 'new';
    public const XNG_STATE_EMAIL_SENT = 'email_sent';
    public const XNG_STATE_SUBSCRIBED = 'subscribed';
    public const XNG_STATE_UNSUBSCRIBED = 'unsubscribed';

    public const XNG_NUMERIC_STATES = [
        self::XNG_STATE_NULL => 0,
        self::XNG_STATE_NEW => 1,
        self::XNG_STATE_EMAIL_SENT => 2,
        self::XNG_STATE_SUBSCRIBED => 3,
        self::XNG_STATE_UNSUBSCRIBED => -1,
    ];

    public const ROUTE_CROSS_ENGAGE_CONFIRM_SUBSCRIPTION = 'ROUTE_CROSS_ENGAGE_CONFIRM_SUBSCRIPTION';
    public const ROUTE_CROSS_ENGAGE_ALREADY_CONFIRMEND = 'ROUTE_CROSS_ENGAGE_ALREADY_CONFIRMEND';
    public const ROUTE_CROSS_ENGAGE_SUBSCRIBE_FAILED = 'ROUTE_CROSS_ENGAGE_SUBSCRIBE_FAILED';

    public const OPT_IN_PATH_SEGMENT = 'OPT_IN_PATH_SEGMENT';
    public const OPT_OUT_PATH_SEGMENT = 'OPT_OUT_PATH_SEGMENT';
    
    public const CROSS_ENGAGE_IMPORT_PATH = 'CROSS_ENGAGE_IMPORT_PATH';
}
