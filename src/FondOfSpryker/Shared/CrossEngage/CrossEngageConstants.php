<?php

namespace FondOfSpryker\Shared\CrossEngage;

interface CrossEngageConstants
{
    public const CROSS_ENGAGE_API_KEY = 'CROSS_ENGAGE_API_KEY';
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

    public const XNG_STATE_NULL = 0;
    public const XNG_STATE_NEW = 1;
    public const XNG_STATE_EMAIL_SENT = 2;
    public const XNG_STATE_SUBSCRIBED = 3;
    public const XNG_STATE_UNSUBSCRIBED = 4;

    public const XNG_STATES = [
        'null' => self::XNG_STATE_NULL,
        'new' => self::XNG_STATE_NEW,
        'email_sent' => self::XNG_STATE_EMAIL_SENT,
        'subscribed' => self::XNG_STATE_SUBSCRIBED,
        'unsubscribed' => self::XNG_STATE_UNSUBSCRIBED,
    ];
}
