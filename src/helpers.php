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

if (!function_exists('base_path')) {
    /**
     * Get the path to the base of the install
     *
     * @return string
     */
    function base_path()
    {
        return container()->get('path.base');
    }
}

if (!function_exists('cache_path')) {
    /**
     * Get the cache path
     *
     * @return string
     */
    function cache_path()
    {
        return container()->get('path.cache');
    }
}

if (!function_exists('content_path')) {
    /**
     * Get the content path
     *
     * @return string
     */
    function content_path()
    {
        return container()->get('path.content');
    }
}

if (!function_exists('public_path')) {
    /**
     * Get the public path
     *
     * @return string
     */
    function public_path()
    {
        return container()->get('path.public');
    }
}

if (!function_exists('templates_path')) {
    /**
     * Get the templates path
     *
     * @return string
     */
    function templates_path()
    {
        return container()->get('path.templates');
    }
}
