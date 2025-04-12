<?php

return [

    'user' => env('SHIB_USER', ''),

    /*
    |--------------------------------------------------------------------------
    | Auto-create Users
    |--------------------------------------------------------------------------
    |
    | If true, users will be automatically created from Shibboleth headers
    | if they do not already exist in the database.
    |
    */

    'middleware' => env('SHIB_MIDDLEWARE', 'shibboleth'),

    /*
    |--------------------------------------------------------------------------
    | Auto-create Users
    |--------------------------------------------------------------------------
    |
    | If true, users will be automatically created from Shibboleth headers
    | if they do not already exist in the database.
    |
    */
    'auto_create_users' =>  env('SHIB_AUTO_CREATE_USERS', true),

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
    'server_key' => env('SHIB_SERVER_KEY', 'SHIB_UID'),

    /*
    |--------------------------------------------------------------------------
    | User Identifier Column
    |--------------------------------------------------------------------------
    |
    | This defines the user model column used for authentication, e.g.:
    | 'uid', 'unity_id', 'username', etc.
    |
    */
    'identifier_key' => env('SHIB_IDENTIFIER_KEY', 'unity_id'),

    /*
    |--------------------------------------------------------------------------
    | Shibboleth Attribute Map
    |--------------------------------------------------------------------------
    |
    | These are the Shibboleth attributes to store in the linked metadata table.
    |
    */
    'map' => [
        'uid'                  => 'SHIB_UID',
        'eptid'                => 'SHIB_EPTID',
        'eppn'                 => 'SHIB_EPPN',
        'cpid'                 => 'SHIB_CPID',
        'email'                => 'SHIB_MAIL',
        'first_name'           => 'SHIB_GIVENNAME',
        'last_name'            => 'SHIB_SN',
        'display_name'         => 'SHIB_DISPLAYNAME',
        'primary'              => 'SHIB_PRIMARY',
        'unaffiliation'        => 'SHIB_UNAFFILIATION',
        'affiliation'          => 'SHIB_AFFILIATION',
        'is_2fa_authenticated' => 'SHIB_2FAUTHED',
        'is_2fa_enrolled'      => 'SHIB_2FENROLL',
        'auth_instant'         => 'Shib-Authentication-Instant',
        'auth_method'          => 'Shib-Authentication-Method',
        'auth_context'         => 'Shib-AuthnContext-Class',
        'identity_provider'    => 'Shib-Identity-Provider',
        'session_id'           => 'Shib-Session-ID',
        'session_index'        => 'Shib-Session-Index',
        'session_expires'      => 'Shib-Session-Expires',
        'session_inactivity'   => 'Shib-Session-Inactivity',
        'application_id'       => 'Shib-Application-ID',
        'handler'              => 'Shib-Handler',
        'member_of'            => 'SHIB_MEMBEROF'
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
