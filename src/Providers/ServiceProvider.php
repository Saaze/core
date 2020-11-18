<?php

namespace Saaze\Providers;

abstract class ServiceProvider
{
    /**
     * @var \DI\Container
     */
    protected $container;

    /**
     * @var array
     */
    private $bindings = [];

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var array
     */
    private $routes = [];

    /**
     * @param \DI\Container $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Add bindings to this service provider.
     *
     * @param array $bindings
     * @return void
     */
    public function setBindings($bindings)
    {
        $this->bindings = $bindings;
    }

    /**
     * Get the bindings for this service provider.
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Add config values to this service provider.
     *
     * @param array $config
     * @return void
     */
    public function setConfig($config)
    {
        $this->config = $config;
    }

    /**
     * Get the config values for this service provider.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

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
