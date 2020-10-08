<?php

namespace Saaze\Entries;

use Saaze\Interfaces\CollectionInterface;
use Saaze\Interfaces\ContentParserInterface;
use Saaze\Interfaces\EntryInterface;
use Saaze\Interfaces\EntryParserInterface;

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
     * @var array
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

        $this->data = $entryParser->parseEntry($this->filePath);
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
        if (!$this->collection) {
            return '';
        }

        $slug = $this->slug() !== 'index' ? $this->slug() : '';

        return rtrim(str_replace('{slug}', $slug, $this->collection->entryRoute()), '/');
    }

    /**
     * @return string
     */
    public function content()
    {
        if (isset($this->data['content'])) {
            return $this->data['content'];
        }

        return $this->contentParser->toHtml($this->data['content_raw']);
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
