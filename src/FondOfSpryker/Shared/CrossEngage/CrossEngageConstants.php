<?php

namespace FondOfSpryker\Shared\CrossEngage;

interface CrossEngageConstants
{
    public const CROSS_ENGAGE_API_KEY = 'CROSS_ENGAGE_API_KEY';
    public const CROSS_ENGAGE_API_HEADER = 'CROSS_ENGAGE_API_HEADER';
    public const CROSS_ENGAGE_API_URI_EVENTS = 'CROSS_ENGAGE_API_URI_EVENTS';


    public const CROSS_ENGAGE_URL = 'CROSS_ENGAGE_URL';
    public const CROSS_ENGAGE_FORM_ID = 'CROSS_ENGAGE_FORM_ID';
    public const CROSS_ENGAGE_LIST_ID = 'CROSS_ENGAGE_LIST_ID';
    public const CROSS_ENGAGE_LOCALIZED_CONFIGS = 'CROSS_ENGAGE_LOCALIZED_CONFIGS';
    public const CROSS_ENGAGE_SUBSCRIBE_PATH = 'CROSS_ENGAGE_SUBSCRIBE_PATH';
    public const CROSS_ENGAGE_CONFIRMATION_PATH = 'CROSS_ENGAGE_CONFIRMATION_PATH';
    public const CROSS_ENGAGE_ALREADY_SUBSCRIBED_PATH = 'CROSS_ENGAGE_ALREADY_SUBSCRIBED_PATH';



    public const CROSS_ENGAGE_API_URI = 'CROSS_ENGAGE_API_URI';
    public const CROSS_ENGAGE_API_URI_FETCH_USER = 'CROSS_ENGAGE_API_URI_FETCH_USER';

    public const CROSS_ENGAGE_API_HEADER_CONTENT_TYPE = '';
    public const CROSS_ENGAGE_API_VERSION = '';

    public const XNG_HEADER_FIELD_CONTENT_TYPE = 'Content-Type';
    public const XNG_HEADER_FIELD_API_VERSION = 'X-XNG-ApiVersion';
    public const XNG_HEADER_FIELD_AUTH_TOKEN = 'X-XNG-AuthToken';

    public const XNG_STATE_NULL = 'null';
    public const XNG_STATE_NEW = 'new';
    public const XNG_STATE_EMAIL_SENT = 'email_send';
    public const XNG_STATE_SUBSCRIBED = 'subscribed';
    public const XNG_STATE_UNSUBSCRIBED = 'unsubscribed';

    public const XNG_NUMERIC_STATES = [
        self::XNG_STATE_NULL => 0,
        self::XNG_STATE_NEW => 1,
        self::XNG_STATE_EMAIL_SENT => 2,
        self::XNG_STATE_SUBSCRIBED => 3,
        self::XNG_STATE_UNSUBSCRIBED => -1,
    ];

    public const XNG_INTERNAL_STATE_CREATED = 'created';

    public const XNG_INTERNAL_STATE_CREATED_FAILED = 'failed to create user on %s';

    public const ROUTE_CROSS_ENGAGE_FOOTER = 'ROUTE_CROSS_ENGAGE_FOOTER';
    public const ROUTE_CROSS_ENGAGE_SUBMIT = 'ROUTE_CROSS_ENGAGE_SUBMIT';
    public const ROUTE_CROSS_ENGAGE_SUBSCRIBE = 'ROUTE_CROSS_ENGAGE_SUBSCRIBE';
    public const ROUTE_CROSS_ENGAGE_SUBSCRIBE_FAILED = 'ROUTE_CROSS_ENGAGE_SUBSCRIBE_FAILED';
    public const ROUTE_CROSS_ENGAGE_SUBSCRIBE_CONFIRM = 'ROUTE_CROSS_ENGAGE_SUBSCRIBE_CONFIRM';


}
