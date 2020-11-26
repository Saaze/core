<?php

namespace Saaze\Routing;

use Saaze\Interfaces\RouteInterface;

class Route implements RouteInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var callable|string
     */
    protected $handler;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var integer
     */
    protected $priority;

    /**
     * @param string $path
     * @param callable|string $handler
     * @param string $method
     * @param integer $priority
     */
    public function __construct($path, $handler, $method = 'GET', $priority = 0)
    {
        $this->path     = $path;
        $this->handler  = $handler;
        $this->method   = strtoupper($method);
        $this->priority = $priority;
    }

    /**
     * Return the route path.
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Return the route handler.
     *
     * @return callable|string
     */
    public function handler()
    {
        return $this->handler;
    }

    /**
     * Return the route HTTP method.
     *
     * @return string
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * Return the route priority.
     *
     * @return integer
     */
    public function priority()
    {
        return $this->priority;
    }
}
