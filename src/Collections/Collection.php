<?php

namespace Saaze\Collections;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Finder\Finder;

class Collection
{
    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $entries;

    /**
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
        $this->parse();
    }

    protected function parse()
    {
        $this->data = Yaml::parse(file_get_contents($this->filePath));
    }

    /**
     * @return string
     */
    public function filePath()
    {
        return $this->filePath;
    }

    /**
     * @return array
     */
    public function data()
    {
        return $this->data;
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
        return $this->data['index_route'] ?? null;
    }

    /**
     * @return bool
     */
    public function indexIsEntry()
    {
        return (bool) (new Finder())
            ->in(SAAZE_CONTENT_PATH . DIRECTORY_SEPARATOR . $this->slug())
            ->files()
            ->name('index.yml')
            ->count();
    }

    /**
     * @return string|null
     */
    public function entryRoute()
    {
        return $this->data['entry_route'] ?? null;
    }
}
