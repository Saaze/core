<?php

namespace Saaze\Interfaces;

interface CollectionInterface
{
    /**
     * Return the collection file path
     *
     * @return string
     */
    public function filePath();

    /**
     * Return the collection data
     *
     * @return array
     */
    public function data();

    /**
     * Return the collection slug
     *
     * @return string
     */
    public function slug();

    /**
     * Return the collection index route
     *
     * @return string|null
     */
    public function indexRoute();

    /**
     * Return the collection entry route
     *
     * @return string|null
     */
    public function entryRoute();

    /**
     * Determine if the collection index is an entry
     *
     * @return bool
     */
    public function indexIsEntry();
}
