<?php

namespace Dfoxx\Shibboleth;

use Illuminate\Support\ServiceProvider;

class ShibbolethServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__.'/migrations');
        $this->app['auth']->provider('shibboleth', function ($app, array $config) {
            $model = $app['config']['auth.providers.users.model'];
            return new ShibbolethUserProvider($app['hash'], $model);
        });

        $this->app['auth']->extend('shibboleth', function ($app, $name, array $config) {
            $model = $app['config']['auth.providers.users.model'];
            $provider = new ShibbolethUserProvider($app['hash'], $model);
            return new ShibbolethGuard($name, $provider, $app['session.store'], $app['request']);
        });
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
