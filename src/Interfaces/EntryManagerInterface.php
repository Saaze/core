<?php

namespace Saaze\Interfaces;

interface EntryManagerInterface
{
    /**
     * Set the collection
     *
     * @param \Saaze\Interfaces\CollectionInterface $collection
     * @return void
     */
    public function setCollection(\Saaze\Interfaces\CollectionInterface $collection);

    /**
     * Return a sorted array of entries
     *
     * @return array
     */
    public function getEntries();

    /**
     * Return a single entry identified by a slug
     *
     * @param string $slug
     * @return \Saaze\Interfaces\EntryInterface|null
     */
    public function getEntry($slug);

    /**
     * Return all of the entries for a template
     *
     * @return array
     */
    public function getEntriesForTemplate();

    /**
     * Paginate a set of entries for a template
     *
     * @param array $entries
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function paginateEntriesForTemplate($entries, $page, $perPage);

    /**
     * Return an entry for a template
     *
     * @param \Saaze\Interfaces\EntryInterface $entry
     * @return array
     */
    public function getEntryForTemplate(\Saaze\Interfaces\EntryInterface $entry);
}
