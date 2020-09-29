<?php

namespace Saaze\Entries;

use Saaze\Entries\Entry;
use Saaze\Collections\Collection;
use Symfony\Component\Finder\Finder;

class EntryManager
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var array
     */
    protected $entries = [];

    /**
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
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
        if (empty($this->collection->data()['sort'])) {
            return;
        }

        $sort      = $this->collection->data()['sort'];
        $field     = $sort['field'] ?? 'title';
        $direction = $sort['direction'] ?? 'asc';

        usort($this->entries, function ($a, $b) use ($field, $direction) {
            if (strtolower($direction) === 'asc') {
                return $a->data()[$field] <=> $b->data()[$field];
            }

            return $b->data()[$field] <=> $a->data()[$field];
        });
    }

    /**
     * @param string $slug
     * @return \Saaze\Entries\Entry|null
     */
    public function getEntry($slug)
    {
        $collectionDir = SAAZE_CONTENT_PATH . DIRECTORY_SEPARATOR . $this->collection->slug();
        if (!is_dir($collectionDir)) {
            return null;
        }

        if (empty($this->entries[$slug])) {
            $entryPath = $collectionDir . DIRECTORY_SEPARATOR . "{$slug}.md";
            $entry = $this->loadEntry($entryPath);

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
        $collectionDir = SAAZE_CONTENT_PATH . DIRECTORY_SEPARATOR . $this->collection->slug();
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
     * @return \Saaze\Entries\Entry|null
     */
    protected function loadEntry($filePath)
    {
        if (!file_exists($filePath)) {
            return null;
        }

        $entry = new Entry($filePath);

        $this->entries[$entry->slug()] = $entry;

        return $entry;
    }

    /**
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public function getEntriesForTemplate($page, $perPage)
    {
        $entries      = $this->getEntries();
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

        $pageEntries = array_map(function ($entry) {
            return $this->getEntryForTemplate($entry);
        }, $pageEntries);

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
     * @param Entry $entry
     * @return array
     */
    public function getEntryForTemplate(Entry $entry)
    {
        $entry->setCollection($this->collection);
        $entryData = $entry->data();

        $entryData['url']     = $entry->url();
        $entryData['content'] = $entry->content();
        $entryData['excerpt'] = $entry->excerpt();

        return $entryData;
    }
}
