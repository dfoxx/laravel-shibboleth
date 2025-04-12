# laravel-shibboleth

An opinionated Shibboleth authentication package for Laravel. There is a middleware and a guard, I prefer the middleware.

> **Note:** This package assumes your web server is configured with the [Shibboleth Service Provider](https://shibboleth.atlassian.net/wiki/spaces/SP3/) (e.g. Apache or NGINX with Shibboleth modules), and that PHP `$_SERVER` variables are populated with authenticated Shibboleth user attributes. The Shibboleth handler (e.g. `/Shibboleth.sso`) must also be properly configured and accessible.

## Features

-   Route-level middleware (`shibboleth.auth`) for lightweight Shibboleth enforcement
-   Custom Laravel guard (`auth:shibboleth`) for seamless integration with Laravel’s auth system
-   Configurable Shibboleth header and field mappings via `config/shibboleth.php`
-   Auto-create users from Shibboleth headers based on configurable identifiers
-   Store extended identity metadata in a dedicated `users_shibboleth` table
-   Promotable attributes like `uid` and `eppn` for fast indexed lookups

## Installation

```bash
composer require dfoxx/laravel-shibboleth
```

**REQUIRED** Update `public/.htaccess`:

```
<IfModule mod_shib>
    AuthType shibboleth
    ShibRequestSetting requireSession false
    require shibboleth
</IfModule>
```

## Configuration

**REQUIRED** Update your project `.env`:

```
SHIB_USER=dfsterli
SHIB_MIDDLEWARE=shibboleth
SHIB_AUTO_CREATE_USERS=false
SHIB_SERVER_KEY=SHIB_UID
SHIB_IDENTIFIER_KEY=unity_id
```

| `.env` key               | Description                                                                     |
| :----------------------- | :------------------------------------------------------------------------------ |
| `SHIB_USER`              | Optional for local development to bypass headers and log in this user           |
| `SHIB_MIDDLEWARE`        | Set your own custom name for the middleware                                     |
| `SHIB_AUTO_CREATE_USERS` | Defaults to false, will not attempt to create users                             |
| `SHIB_SERVER_KEY`        | Shibboleth header used to uniquely identify the user (e.g. SHIB_UID, SHIB_EPPN) |
| `SHIB_IDENTIFIER_KEY`    | User model column to use for authentication (e.g. uid, unity_id, username)      |

Or publish the config file `config/shibboleth.php` and edit the values you need:

```bash
php artisan vendor:publish --tag=laravel-shibboleth-config
```

## Traits

**REQUIRED** Update User model with this trait to use `SHIB_IDENTIFIER_KEY` as the column to store the Shibboleth identifier

```php
use Dfoxx\Shibboleth\HasShibbolethIdentifier;

class User extends Authenticatable
{
    use HasShibbolethIdentifier;
}
```

If you decide to store the Shibboleth data (migration is provided) you can use this trait

```php
use Dfoxx\Shibboleth\HasShibbolethData;

class User extends Authenticatable
{
    use HasShibbolethData;
}
```

Access Shibboleth data:

```php
$user->shib('eppn');
$user->shibboleth->attributes['eppn'];
```

## Migrations

This package provides two optional migrations. You probably don't need them.

```bash
php artisan vendor:publish --tag=laravel-shibboleth-migrations
```

## Middleware `shibboleth`

This middleware checks for a Shibboleth-authenticated user via PHP `$_SERVER` variables.

Protect routes:

```php
Route::middleware(['shibboleth'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'));
});
```

## Guard `auth:shibboleth`

In `config/auth.php`:

```php
'guards' => [
    'shibboleth' => [
        'driver' => 'shibboleth-session',
        'provider' => 'users',
    ],
],
```

Then protect routes:

```php
Route::middleware(['auth:shibboleth'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'));
});
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
