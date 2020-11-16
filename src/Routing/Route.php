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
     *
     * @var array
     */
    protected $params;

    /**
     *
     * @var array
     */
    protected $requirements;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @param string $path
     * @param array $params
     * @param array $requirements
     * @param int $priority
     */
    public function __construct($path, $params = [], $requirements = [], $priority = 0)
    {
        $this->path         = $path;
        $this->params       = $params;
        $this->requirements = $requirements;
        $this->priority     = $priority;
    }

    /**
     * Get the route path
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    /**
     * Get the route parameters
     *
     * @return array
     */
    public function params()
    {
        return $this->params;
    }

    /**
     * Get the route requirements
     *
     * @return array
     */
    public function requirements()
    {
        return $this->requirements;
    }

    /**
     * Get the route priority
     *
     * @return int
     */
    public function priority()
    {
        return $this->priority;
    }
}
