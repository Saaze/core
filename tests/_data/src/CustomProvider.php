<?php

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
        $this->addConfig([
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
        $this->addRoute('/custom-route', function () {
            return response('Hello World!');
        });
    }
}
