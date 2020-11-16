<?php

namespace Saaze\Interfaces;

interface RouteInterface
{
    /**
     * Create a new route
     *
     * @param string $path
     * @param array $params
     * @param array $requirements
     * @param int $priority
     */
    public function __construct($path, $params = [], $requirements = [], $priority = 0);

    /**
     * Get the route path
     *
     * @return string
     */
    public function path();

    /**
     * Get the route parameters
     *
     * @return string
     */
    public function params();

    /**
     * Get the route requirements
     *
     * @return string
     */
    public function requirements();

    /**
     * Get the route priority
     *
     * @return int
     */
    public function priority();
}
