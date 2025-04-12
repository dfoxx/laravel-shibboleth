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

## Configuration

Update your project `.env`:

| `.env` key        | Description                                                                     |
| ----------------- | ------------------------------------------------------------------------------- |
| `APP_USER`        | Optional for local development to bypass headers and log in this user           |
| `APP_USER_FIELD`  | Shibboleth header used to uniquely identify the user (e.g. SHIB_UID, SHIB_EPPN) |
| `APP_USER_COLUMN` | User model column to use for authentication (e.g. uid, unity_id, username)      |

Examples:

```
APP_USER=dfsterli
APP_USER_FIELD=SHIB_UID
APP_USER_COLUMN=unity_id
```

Or publish the config file `config/shibboleth.php` and edit the values you need:

```bash
php artisan vendor:publish --tag=laravel-shibboleth-config
```

## Traits

You need this trait, which will use `APP_USER_COLUMN` as the column to store the Shibboleth identifier

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

## Middleware `shibboleth.auth`

This middleware checks for a Shibboleth-authenticated user via PHP `$_SERVER` variables.

Register the middleware in your `AppServiceProvider` or your own service provider:

```php
$this->app['router']->aliasMiddleware('shibboleth.auth', \Dfoxx\Shibboleth\Authenticate::class);
```

Protect routes:

```php
Route::middleware(['shibboleth.auth'])->group(function () {
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
