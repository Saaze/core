<?php

namespace Saaze\Interfaces;

interface CollectionManagerInterface
{
    /**
     * Return a sorted array of collections
     *
     * @return array
     */
    public function getCollections();

    /**
     * Return a single collection identified by a slug
     *
     * @param string $slug
     * @return \Saaze\Interfaces\CollectionInterface|null
     */
    public function getCollection($slug);
}
