# NC State Shibboleth for Laravel

This package is provide some basic guidance on setting up authentication middleware,
custom guard, and user provider for Shibboleth in Laravel 5.4.

## Install

Via Composer

``` bash
$ composer require dfoxx/laravel-shibboleth
```

Then add the service provider in `config/app.php`:
``` php
Dfoxx\Shibboleth\ShibbolethServiceProvider::class,
```
You must add the `unity_id` to the `$fillable` array in `app/User.php`:
``` php
protected $fillable = [
        'unity_id', 'name', 'email',
];
```

You must specify the identifier on the User model by adding the following to `app/User.php`:
``` php
    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return 'unity_id';
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->unity_id;
    }
```

Edit `config/auth.php`:
``` php
'guards' => [
        'web' => [
            'driver' => 'shibboleth',
            'provider' => 'users',
        ],
],

'providers' => [
        'users' => [
            'driver' => 'shibboleth',
            'model' => App\User::class,
        ],
],
```

Add default user for testing to `config/services.php`:
```php
'shib' => [
    'default_user' => env('APP_USER')
],
```

Add middleware to the `app/Http/Kernel.php`:
``` php
'auth.shib' => \Dfoxx\Shibboleth\AuthenticateWithShibboleth::class,
```

## Usage

When the app environment is set to local `APP_ENV=local` in `.env` can add the value
`APP_USER=userid` to specify which user you want to log in as.

To configure routes that use the middleware see this example:
``` php
Route::group(['middleware' => 'auth.shib'], function() {

    Route::get('/home', 'HomeController@index');

});
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
