<?php

namespace Saaze\Entries;

use Symfony\Component\Yaml\Yaml;
use Saaze\Interfaces\CollectionInterface;
use Saaze\Interfaces\ContentParserInterface;
use Saaze\Interfaces\EntryInterface;

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
    public function __construct($filePath, ContentParserInterface $contentParser)
    {
        $this->filePath      = $filePath;
        $this->contentParser = $contentParser;

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
        return basename($this->filePath, '.md');
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
