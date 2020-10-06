<?php

if (!function_exists('container')) {
    /**
     * Get the container instance
     *
     * @return mixed
     */
    function container()
    {
        return \Saaze\Container\Container::getInstance();
    }
}

