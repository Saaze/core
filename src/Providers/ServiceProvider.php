<?php

namespace Saaze\Providers;

abstract class ServiceProvider
{
    /**
     * @var array
     */
    protected $bindings = [];

    /**
     * @var array
     */
    private $config = [];

    /**
     * @var array
     */
    private $routes = [];

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
     * Get the bindings.
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Add items to the config.
     *
     * @return array
     */
    public function addConfig($config)
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * Get the config.
     *
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Add a route.
     *
     * @param string $method
     * @param string $path
     * @param callable|string $handler
     * @param integer $priority
     * @return void
     */
    public function addRoute($method, $path, $handler, $priority = 0)
    {
        $this->routes[$method.$path] = [
            'method'   => $method,
            'path'     => $path,
            'handler'  => $handler,
            'priority' => $priority,
        ];
    }

    /**
     * Get the routes.
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}
