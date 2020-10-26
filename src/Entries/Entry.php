<?php

namespace Saaze\Entries;

use Adbar\Dot;
use Saaze\Interfaces\EntryInterface;
use Saaze\Interfaces\CollectionInterface;
use Saaze\Interfaces\EntryParserInterface;
use Saaze\Interfaces\ContentParserInterface;

class Entry implements EntryInterface
{
    /**
     * @var CollectionInterface|null
     */
    protected $collection = null;

    /**
     * @var string
     */
    protected $filePath;

    /**
     * @var Dot
     */
    protected $data;

    /**
     * @var ContentParserInterface
     */
    protected $contentParser;

    /**
     * @param string $filePath
     */
    public function __construct($filePath, ContentParserInterface $contentParser, EntryParserInterface $entryParser)
    {
        $this->filePath      = $filePath;
        $this->contentParser = $contentParser;

        $this->data = new Dot($entryParser->parseEntry($this->filePath));
    }

    /**
     * @param CollectionInterface $collection
     */
    public function setCollection(CollectionInterface $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return CollectionInterface|null
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
        $slug = substr($this->filePath, 0, strrpos($this->filePath, '.'));
        $slug = str_replace(content_path(), '', $slug);
        $slug = str_replace("/{$this->collection->slug()}", '', $slug);
        $slug = ltrim($slug, '/');

        return $slug;
    }

    /**
     * @return string
     */
    public function url()
    {
        if ($this->data->has('url')) {
            return $this->data('url');
        }

        $slug = $this->slug();

        if (substr_compare($this->slug(), 'index', -strlen('index')) === 0) {
            $slug = preg_replace('/index$/', '', $slug);
        }

        return rtrim(str_replace('{slug}', $slug, $this->collection->entryRoute()), '/');
    }

    /**
     * @return string
     */
    public function content()
    {
        if ($this->data->has('content')) {
            return $this->data('content');
        }

        return $this->contentParser->toHtml($this->data('content_raw'));
    }

    /**
     * @param integer $length
     * @return string
     */
    public function excerpt($length = 300)
    {
        if ($this->data->has('excerpt')) {
            return $this->data('excerpt');
        }

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
