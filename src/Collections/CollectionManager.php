<?php

namespace Saaze\Collections;

use Saaze\Interfaces\CollectionInterface;
use Saaze\Interfaces\CollectionManagerInterface;
use Symfony\Component\Finder\Finder;

class CollectionManager implements CollectionManagerInterface
{
    /**
     * @var array
     */
    protected $collections = [];

    /**
     * @return array
     */
    public function getCollections()
    {
        if (empty($this->collections)) {
            $this->loadCollections();
        }
        if (empty($this->collections)) {
            return $this->collections;
        }

        $this->sortCollections();

        return $this->collections;
    }

    protected function sortCollections()
    {
        uasort($this->collections, function ($a, $b) {
            return count(explode('/', $b->entryRoute())) <=> count(explode('/', $a->entryRoute()));
        });
    }

    /**
     * @param string $slug
     * @return \Saaze\Interfaces\CollectionInterface|null
     */
    public function getCollection($slug)
    {
        $this->getCollections();

        if (empty($this->collections[$slug])) {
            return null;
        }

        return $this->collections[$slug];
    }

    /**
     * @return array
     */
    protected function loadCollections()
    {
        $paths = (new Finder())->in(content_path())->files()->name('*.yml')->depth(0);

        foreach ($paths as $file) {
            $this->loadCollection($file->getPathname());
        }

        return $this->collections;
    }

    /**
     * @param string $filePath
     * @return \Saaze\Interfaces\CollectionInterface|null
     */
    protected function loadCollection($filePath)
    {
        if (!file_exists($filePath)) {
            return null;
        }

        $collection = container()->make(CollectionInterface::class, ['filePath' => $filePath]);

        $this->collections[$collection->slug()] = $collection;

        return $collection;
    }
}
