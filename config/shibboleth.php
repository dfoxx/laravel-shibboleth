<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Auto-create Users
    |--------------------------------------------------------------------------
    |
    | If true, users will be automatically created from Shibboleth headers
    | if they do not already exist in the database.
    |
    */
    'auto_create_users' => true,

    /*
    |--------------------------------------------------------------------------
    | Header Mapping
    |--------------------------------------------------------------------------
    |
    | These map Shibboleth headers to user model attributes.
    |
    */
    'headers' => [
        'username' => 'REMOTE_USER',
        'email'    => 'SHIB_MAIL',
    ],

    /*
    |--------------------------------------------------------------------------
    | Identifier Header Key
    |--------------------------------------------------------------------------
    |
    | This value determines which $_SERVER key should be used to uniquely
    | identify the user. If missing, the guard will try to auto-detect a
    | matching key like SHIB_UID.
    |
    */
    'identifier' => env('APP_USER_FIELD', 'SHIB_UID'),

    /*
    |--------------------------------------------------------------------------
    | User Identifier Column
    |--------------------------------------------------------------------------
    |
    | This defines the user model column used for authentication, e.g.:
    | 'uid', 'unity_id', 'username', etc.
    |
    */
    'identifier_column' => env('APP_USER_COLUMN', 'unity_id'),

    /*
    |--------------------------------------------------------------------------
    | Shibboleth Attribute Fields
    |--------------------------------------------------------------------------
    |
    | These are the Shibboleth attributes to store in the linked metadata table.
    |
    */
    'fields' => [
        'uid'                  => 'SHIB_UID',
        'eptid'                => 'SHIB_EPTID',
        'eppn'                 => 'SHIB_EPPN',
        'cpid'                 => 'SHIB_CPID',
        'given_name'           => 'SHIB_GIVENNAME',
        'surname'              => 'SHIB_SN',
        'display_name'         => 'SHIB_DISPLAYNAME',
        'unaffiliation'        => 'SHIB_UNAFFILIATION',
        'affiliation'          => 'SHIB_AFFILIATION',
        'is_2fa_authenticated' => 'SHIB_2FAUTHED',
        'is_2fa_enrolled'      => 'SHIB_2FENROLL',
        'authn_instant'        => 'Shib-Authentication-Instant',
        'authn_method'         => 'Shib-Authentication-Method',
        'authn_context'        => 'Shib-AuthnContext-Class',
        'identity_provider'    => 'Shib-Identity-Provider',
        'session_id'           => 'Shib-Session-ID',
        'session_index'        => 'Shib-Session-Index',
        'session_expires'      => 'Shib-Session-Expires',
        'session_inactivity'   => 'Shib-Session-Inactivity',
        'application_id'       => 'Shib-Application-ID',
        'handler'              => 'Shib-Handler',
    ],

    /*
    |--------------------------------------------------------------------------
    | Promoted Fields
    |--------------------------------------------------------------------------
    |
    | These attributes will also be stored in dedicated indexed columns
    | for fast querying (e.g. by uid, eppn, session index).
    |
    */
    'promoted_fields' => [
        'uid',
        'eptid',
        'eppn',
        'cpid',
        'session_index',
        'identity_provider',
    ],
];
