<?php

if (!function_exists('cache_path')) {
    /**
     * Get the cache path
     *
     * @return string
     */
    function cache_path()
    {
        return app()->basePath().'/cache';
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
        return app()->basePath().'/content';
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
        return app()->basePath().'/public';
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
        return app()->basePath().'/templates';
    }
}
