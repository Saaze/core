<?php

use Saaze\Providers\ServiceProvider;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
        $this->addRoute('GET', '/custom-route', function (ServerRequestInterface $request) : ResponseInterface {
            $response = new \Laminas\Diactoros\Response;
            $response->getBody()->write('<h1>Hello, World!</h1>');
            return $response;
        });
    }
}
