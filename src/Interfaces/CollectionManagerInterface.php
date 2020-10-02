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
     * @return \Saaze\Collections\Collection|null
     */
    public function getCollection($slug);
}
