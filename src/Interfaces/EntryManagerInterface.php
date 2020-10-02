<?php

namespace Saaze\Interfaces;

interface EntryManagerInterface
{
    /**
     * Set the collection
     *
     * @param \Saaze\Collections\Collection $collection
     * @return void
     */
    public function setCollection(\Saaze\Collections\Collection $collection);

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
     * @return \Saaze\Entries\Entry|null
     */
    public function getEntry($slug);

    /**
     * Return a paginated set of entry results for a template
     *
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getEntriesForTemplate($page, $perPage);

    /**
     * Return an entry for a template
     *
     * @param \Saaze\Entries\Entry $entry
     * @return array
     */
    public function getEntryForTemplate(\Saaze\Entries\Entry $entry);
}
