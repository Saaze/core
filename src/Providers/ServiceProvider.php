<?php

namespace Saaze\Providers;

use Saaze\Container\Container;

abstract class ServiceProvider
{
    /**
     * @var \DI\Container
     */
    protected $container;

    /**
     * @var array
     */
    private $routes = [];

    public function __construct()
    {
        $this->container = Container::getInstance();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    abstract public function boot();

    /**
     * Add routes to this service provider.
     *
     * @param array $routes
     * @return void
     */
    public function setRoutes($routes)
    {
        $this->routes = $routes;
    }

    /**
     * Get the routes for this service provider.
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
