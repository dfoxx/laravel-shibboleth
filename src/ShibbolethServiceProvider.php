<?php

namespace Dfoxx\Shibboleth;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ShibbolethServiceProvider extends PackageServiceProvider
{
    public function boot()
    {
        $this->app['router']->aliasMiddleware(config('shibboleth.middleware'), Authenticate::class);

        $this->app['auth']->provider('shibboleth', function ($app, array $config) {
            return new ShibbolethUserProvider($config['model']);
        });

        $this->app['auth']->extend('shibboleth-session', function ($app, $name, array $config) {
            $provider = Auth::createUserProvider($config['provider']);

            return new ShibbolethGuard(
                $name,
                $provider,
                $app['session.store'],
                $app['request'],
                $app['events']
            );
        });
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-shibboleth')
            ->hasConfigFile('shibboleth')
            ->hasMigrations([
                'users_shibboleth_data_table',
                'users_table',
            ]);
    }
}
