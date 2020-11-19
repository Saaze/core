<?php

namespace Saaze\Container;

use DI\ContainerBuilder;

class Container
{
    /**
     * @var static
     */
    protected static $instance;

    /**
     * @var \DI\Container
     */
    protected $container;

    protected function __construct()
    {
        if (!defined('SAAZE_PATH')) {
            throw new \Exception('SAAZE_PATH is not defined');
        }

        $builder = new ContainerBuilder();
        $builder->useAnnotations(false);
        $this->container = $builder->build();

        $this->resolveProviders();
        $this->registerProviders();
    }

    /**
     * @return \DI\Container
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new static;
        }

        return static::$instance->container;
    }

    /**
     * @return void
     */
    protected function resolveProviders()
    {
        $providers = [];

        foreach ($this->getProviders() as $providerClass) {
            $providers[] = new $providerClass($this->container);
        }

        $this->container->set('providers', $providers);
    }

    /**
     * @return array
     */
    protected function getProviders()
    {
        $providers = [
            \Saaze\Providers\SaazeServiceProvider::class,
        ];

        if (!file_exists(SAAZE_PATH . '/bootstrap.php')) {
            return $providers;
        }

        $bootstrap = require_once(SAAZE_PATH . '/bootstrap.php');
        if (!empty($bootstrap['providers']) && is_array($bootstrap['providers'])) {
            $providers = array_merge($providers, $bootstrap['providers']);
        }

        return $providers;
    }

    /**
     * @return void
     */
    protected function registerProviders()
    {
        foreach ($this->container->get('providers') as $provider) {
            $provider->register();

            foreach ($provider->getBindings() as $key => $value) {
                if (is_string($value)) {
                    $this->container->set($key, \DI\autowire($value));
                }
            }
            foreach ($provider->getConfig() as $key => $value) {
                $this->container->set($key, $value);
            }
        }
    }

    /**
     * @return void
     */
    public static function bootProviders()
    {
        $routes = [];

        foreach (self::getInstance()->get('providers') as $provider) {
            $provider->boot();

            $routes = array_merge($routes, $provider->getRoutes());
        }

        usort($routes, function ($a, $b) {
            return $b->priority() <=> $a->priority();
        });

        self::getInstance()->set('routes', $routes);
    }
}
