<?php

namespace Saaze\Entries;

use Parsedown;
use Symfony\Component\Yaml\Yaml;
use Saaze\Collections\Collection;

class Entry
{
    /**
     * @var Collection|null
     */
    protected $collection;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var array
     */
    protected $data;

    /**
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        $this->collection = null;
        $this->filePath = $filePath;
        $this->parse();
    }

    protected function parse()
    {
        $content = file_get_contents($this->filePath);
        $data    = explode('---', $content, 2);

        $this->data = Yaml::parse($data[0]);
        $this->data['content_raw'] = $data[1] ?? '';
    }

    /**
     * @param Collection $collection
     */
    public function setCollection(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return Collection|null
     */
    public function getCollection()
    {
        return $this->collection;
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
        return basename($this->filePath, '.md');
    }

    /**
     * @return string
     */
    public function url()
    {
        if ($this->slug() === 'index') {
            return '/';
        }
        if (!$this->collection) {
            return '';
        }

        return str_replace('{slug}', $this->slug(), $this->collection->entryRoute());
    }

    /**
     * @return string
     */
    public function content()
    {
        if (isset($this->data['content'])) {
            return $this->data['content'];
        }

        return Parsedown::instance()
            ->setBreaksEnabled(true)
            ->text($this->data['content_raw']);
    }

    /**
     * @param integer $length
     * @return string
     */
    public function excerpt($length = 300)
    {
        $content = $this->content();
        if (!$content) {
            return $content;
        }

        $excerpt = strip_tags($content);

        if (strlen($excerpt) > $length) {
            $excerptCut = substr($excerpt, 0, $length);
            $endPoint   = strrpos($excerptCut, ' ');
            $excerpt    = $endPoint ? substr($excerptCut, 0, $endPoint) : substr($excerptCut, 0);
            $excerpt    .= '&hellip;';
        }

        return $excerpt;
    }
}
