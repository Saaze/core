<?php

namespace Saaze\Interfaces;

interface RouteInterface
{
    /**
     * Return the route HTTP method.
     *
     * @return string
     */
    public function method();

    /**
     * Return the route path.
     *
     * @return string
     */
    public function path();

    /**
     * Return the route handler.
     *
     * @return callable|string
     */
    public function handler();

    /**
     * Return the route priority.
     *
     * @return integer
     */
    public function priority();
}
