<?php

namespace Saaze\Interfaces;

use Saaze\Interfaces\CollectionInterface;

interface EntryInterface
{
    /**
     * Set the collection for this entry
     *
     * @param CollectionInterface $collection
     */
    public function setCollection(CollectionInterface $collection);

    /**
     * Get the collection
     *
     * @return CollectionInterface|null
     */
    public function getCollection();

    /**
     * Return the entry file path
     *
     * @return string
     */
    public function filePath();

    /**
     * Return the entry data
     *
     * @return array
     */
    public function data();

    /**
     * Return the entry slug
     *
     * @return string
     */
    public function slug();

    /**
     * Return the entry URL
     *
     * @return string
     */
    public function url();

    /**
     * Return the entry content in HTML
     *
     * @return string
     */
    public function content();

    /**
     * Return the entry excerpt in HTML
     *
     * @param integer $length
     * @return string
     */
    public function excerpt($length = 300);
}
