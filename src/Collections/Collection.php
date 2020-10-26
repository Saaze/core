<?php

namespace Saaze\Collections;

use Adbar\Dot;
use Symfony\Component\Finder\Finder;
use Saaze\Interfaces\CollectionInterface;
use Saaze\Interfaces\CollectionParserInterface;

class Collection implements CollectionInterface
{
    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var Dot
     */
    protected $data;

    /**
     * @var array
     */
    protected $entries;

    /**
     * @param string $filePath
     */
    public function __construct($filePath, CollectionParserInterface $collectionParser)
    {
        $this->filePath = $filePath;

        $this->data = new Dot($collectionParser->parseCollection($this->filePath));
    }

    /**
     * @return string
     */
    public function filePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function data($key = '', $default = null)
    {
        if ($key) {
            return $this->data->get($key, $default);
        }

        return $this->data->all();
    }

    /**
     * @return string
     */
    public function slug()
    {
        return basename($this->filePath, '.yml');
    }

    /**
     * @return string|null
     */
    public function indexRoute()
    {
        return $this->data('index_route');
    }

    /**
     * @return string|null
     */
    public function entryRoute()
    {
        return $this->data('entry_route');
    }

    /**
     * @return bool
     */
    public function indexIsEntry()
    {
        return (bool) (new Finder())
            ->in(content_path() . '/' . $this->slug())
            ->files()
            ->name('index.md')
            ->count();
    }
}
