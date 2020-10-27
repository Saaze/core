<?php

namespace Saaze\Entries;

use Saaze\Interfaces\EntryInterface;
use Saaze\Interfaces\CollectionInterface;
use Saaze\Interfaces\EntryManagerInterface;
use Symfony\Component\Finder\Finder;

class EntryManager implements EntryManagerInterface
{
    /**
     * @var CollectionInterface
     */
    protected $collection;

    /**
     * @var array
     */
    protected $entries = [];

    /**
     * @param \Saaze\Interfaces\CollectionInterface $collection
     * @return void
     */
    public function setCollection(CollectionInterface $collection)
    {
        $this->collection = $collection;
        $this->entries    = [];
    }

    /**
     * @return array
     */
    public function getEntries()
    {
        if (empty($this->entries)) {
            $this->loadEntries();
        }
        if (empty($this->entries)) {
            return $this->entries;
        }

        $this->sortEntries();

        return $this->entries;
    }

    protected function sortEntries()
    {
        if (empty($this->collection->data('sort'))) {
            return;
        }

        $field     = $this->collection->data('sort.field', 'title');
        $direction = $this->collection->data('sort.direction', 'asc');

        usort($this->entries, function ($a, $b) use ($field, $direction) {
            $aData = $a->data($field);
            $bData = $b->data($field);

            if (strtolower($direction) === 'asc') {
                return $aData <=> $bData;
            }

            return $bData <=> $aData;
        });
    }

    /**
     * @param string $slug
     * @return EntryInterface|null
     */
    public function getEntry($slug)
    {
        $collectionDir = content_path() . '/' . $this->collection->slug();
        if (!is_dir($collectionDir)) {
            return null;
        }

        if (empty($this->entries[$slug])) {
            $entryPath = $collectionDir . "/{$slug}.md";
            $entry = $this->loadEntry($entryPath);

            if (!$entry) {
                $entryPath = $collectionDir . "/{$slug}/index.md";
                $entry = $this->loadEntry($entryPath);
            }

            if ($entry) {
                $this->entries[$slug] = $entry;
            }
        }

        return $this->entries[$slug] ?? null;
    }

    /**
     * @return array
     */
    protected function loadEntries()
    {
        $collectionDir = content_path() . '/' . $this->collection->slug();
        if (!is_dir($collectionDir)) {
            return [];
        }

        $paths = (new Finder())->in($collectionDir)->files()->name('*.md');

        foreach ($paths as $file) {
            $this->loadEntry($file->getPathname());
        }

        return $this->entries;
    }

    /**
     * @param string $filePath
     * @return EntryInterface|null
     */
    protected function loadEntry($filePath)
    {
        if (!file_exists($filePath)) {
            return null;
        }

        $entry = container()->make(EntryInterface::class, ['filePath' => $filePath]);
        $entry->setCollection($this->collection);

        $this->entries[$entry->slug()] = $entry;

        return $entry;
    }

    /**
     * @return array
     */
    public function getEntriesForTemplate()
    {
        $entries = $this->getEntries();

        $entries = array_map(function ($entry) {
            return $this->getEntryForTemplate($entry);
        }, $entries);

        return $entries;
    }

    /**
     * @param array $entries
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function paginateEntriesForTemplate($entries, $page, $perPage)
    {
        $totalEntries = count($entries);

        if ($page < 1) {
            $page = 1;
        }
        if ($perPage < 1) {
            $perPage = 1;
        }

        $totalPages = ceil($totalEntries / $perPage);
        $prevPage   = $page > 1 ? $page - 1 : $page;
        $nextPage   = $page < $totalPages ? $page + 1 : $totalPages;

        $pageEntries    = [];
        $pageIndex      = $page - 1;
        $chunkedEntries = array_chunk($entries, $perPage);
        if (isset($chunkedEntries[$pageIndex])) {
            $pageEntries = $chunkedEntries[$pageIndex];
        }

        return [
            'currentPage'  => $page,
            'prevPage'     => $prevPage,
            'nextPage'     => $nextPage,
            'prevUrl'      => $page != $prevPage ? $this->collection->indexRoute() . "/page/{$prevPage}" : '',
            'nextUrl'      => $page != $nextPage ? $this->collection->indexRoute() . "/page/{$nextPage}" : '',
            'perPage'      => $perPage,
            'totalEntries' => $totalEntries,
            'totalPages'   => $totalPages,
            'entries'      => $pageEntries,
        ];
    }

    /**
     * @param EntryInterface $entry
     * @return array
     */
    public function getEntryForTemplate(EntryInterface $entry)
    {
        $entry->setCollection($this->collection);
        $entryData = $entry->data();

        $entryData['url']     = $entry->url();
        $entryData['content'] = $entry->content();
        $entryData['excerpt'] = $entry->excerpt();

        return $entryData;
    }
}
