<?php

namespace Saaze\Providers;

use Saaze\Interfaces\RouteInterface;

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
     * @param string $path
     * @param callable|string $handler
     * @param string $method
     * @param integer $priority
     * @return void
     */
    public function addRoute($path, $handler, $method = 'GET', $priority = 0)
    {
        $this->routes[$method.$path] = container()->make(RouteInterface::class, [
            'path'     => $path,
            'handler'  => $handler,
            'method'   => $method,
            'priority' => $priority,
        ]);
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
