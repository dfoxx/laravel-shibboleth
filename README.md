# laravel-shibboleth

An opinionated Shibboleth authentication package for Laravel. There is a middleware and a guard, I prefer the middleware.

> **Note:** This package assumes your web server is configured with the [Shibboleth Service Provider](https://shibboleth.atlassian.net/wiki/spaces/SP3/) (e.g. Apache or NGINX with Shibboleth modules), and that PHP `$_SERVER` variables are populated with authenticated Shibboleth user attributes. The Shibboleth handler (e.g. `/Shibboleth.sso`) must also be properly configured and accessible.

## Features

-   Route-level middleware (`shibboleth`) for lightweight Shibboleth enforcement
-   Laravel guard (`auth:shibboleth`) for seamless integration with Laravel’s auth system
-   Store extended identity metadata in a dedicated `users_shibboleth` table

## Installation

```bash
composer require dfoxx/laravel-shibboleth
```

Update `public/.htaccess`:

```
<IfModule mod_shib>
    AuthType shibboleth
    ShibRequestSetting requireSession false
    require shibboleth
</IfModule>
```

Update User model with this trait to use `SHIB_IDENTIFIER_KEY` as the column to store the Shibboleth identifier

```php
use Dfoxx\Shibboleth\HasShibbolethIdentifier;

class User extends Authenticatable
{
    use HasShibbolethIdentifier;
}
```

## Middleware

Routes:

```php
// unprotected routes

Route::middleware(['shibboleth'])->group(function () {
    // protected routes
});
```

## Guard

Update `config/auth.php` to use the guard:

```php
'guards' => [
    'shibboleth' => [
        'driver' => 'shibboleth-session',
        'provider' => 'shibboleth',
    ],
],
```

Routes:

```php
// unprotected routes

Route::middleware(['auth:shibboleth'])->group(function () {
    // protected routes
});
```

## Shibboleth Data

You can opt to store the Shibboleth data in it's own model `Shibboleth.php`

You can copy the migrations over and edit them as you see fit:

```bash
php artisan vendor:publish --tag=laravel-shibboleth-migrations
```

Then add the trait to the User model:

```php
use Dfoxx\Shibboleth\HasShibbolethData;

class User extends Authenticatable
{
    use HasShibbolethData;
}
```

And then access Shibboleth data:

```php
$user->shib('eppn');
$user->shibboleth->data['eppn'];
```

## Configuration

| `.env` key               | Description                                                                     |
| :----------------------- | :------------------------------------------------------------------------------ |
| `SHIB_USER`              | Optional for local development to bypass headers and log in this user           |
| `SHIB_MIDDLEWARE`        | Set your own custom name for the middleware                                     |
| `SHIB_AUTO_CREATE_USERS` | Defaults to false, will not attempt to create users                             |
| `SHIB_SERVER_KEY`        | Shibboleth header used to uniquely identify the user (e.g. SHIB_UID, SHIB_EPPN) |
| `SHIB_IDENTIFIER_KEY`    | User model column to use for authentication (e.g. uid, unity_id, username)      |

You can publish the config file `config/shibboleth.php` to edit the map for the Shibboleth model:

```bash
php artisan vendor:publish --tag=laravel-shibboleth-config
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
