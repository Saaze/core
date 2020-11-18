<?php

use Saaze\Interfaces\RouteInterface;
use Saaze\Providers\ServiceProvider;

class CustomProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->setConfig([
            'config.custom_value' => 'foo',
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $routes = [
            $this->container->make(RouteInterface::class, [
                'path' => '/custom-route',
            ])
        ];

        $this->setRoutes($routes);
    }
}
