<?php

namespace Saaze\Tests\Entries;

use Saaze\Tests\TestCase;
use Saaze\Interfaces\CollectionManagerInterface;
use Saaze\Interfaces\EntryManagerInterface;

class EntryTest extends TestCase
{
    /**
     * @var CollectionManagerInterface
     */
    protected $collectionManager;

    /**
     * @var EntryManagerInterface
     */
    protected $entryManager;

    /**
     * @var \Saaze\Interfaces\EntryInterface
     */
    protected $entry;

    public function setUp(): void
    {
        parent::setUp();

        $this->collectionManager = $this->container->get(CollectionManagerInterface::class);
        $this->entryManager      = $this->container->get(EntryManagerInterface::class);

        $this->entry = $this->getEntry('posts', 'example-post-1');
    }

    /**
     * @param string $collectionSlug
     * @param string $entrySlug
     * @return \Saaze\Interfaces\EntryInterface
     */
    private function getEntry($collectionSlug, $entrySlug)
    {
        $collection = $this->collectionManager->getCollection($collectionSlug);
        $this->entryManager->setCollection($collection);
        $entry = $this->entryManager->getEntry($entrySlug);
        $entry->setCollection($collection);

        return $entry;
    }

    public function testData()
    {
        $data = $this->entry->data();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('date', $data);
        $this->assertArrayHasKey('content_raw', $data);
    }

    public function testSlug()
    {
        $this->assertEquals('example-post-1', $this->entry->slug());

        $entry = $this->getEntry('pages', 'index');
        $this->assertEquals('index', $entry->slug());

        $entry = $this->getEntry('pages', 'level1/index');
        $this->assertEquals('level1/index', $entry->slug());

        $entry = $this->getEntry('pages', 'level1/page');
        $this->assertEquals('level1/page', $entry->slug());

        $entry = $this->getEntry('pages', 'level1/level2/page');
        $this->assertEquals('level1/level2/page', $entry->slug());

        $entry = $this->getEntry('docs', 'level1/index');
        $this->assertEquals('level1/index', $entry->slug());

        $entry = $this->getEntry('docs', 'level1/page');
        $this->assertEquals('level1/page', $entry->slug());

        $entry = $this->getEntry('docs', 'level1/level2/page');
        $this->assertEquals('level1/level2/page', $entry->slug());
    }

    public function testUrl()
    {
        $this->assertEquals('/blog/example-post-1', $this->entry->url());

        $entry = $this->getEntry('pages', 'level1/page');
        $this->assertEquals('/level1/page', $entry->url());

        $entry = $this->getEntry('pages', 'level1/level2/page');
        $this->assertEquals('/level1/level2/page', $entry->url());

        $entry = $this->getEntry('docs', 'level1/index');
        $this->assertEquals('/docs/sub/level1', $entry->url());

        $entry = $this->getEntry('docs', 'level1/page');
        $this->assertEquals('/docs/sub/level1/page', $entry->url());

        $entry = $this->getEntry('docs', 'level1/level2/page');
        $this->assertEquals('/docs/sub/level1/level2/page', $entry->url());
    }

    public function testUrlIsIndex()
    {
        $entry = $this->getEntry('docs', 'index');
        $this->assertEquals('/docs/sub', $entry->url());

        $entry = $this->getEntry('pages', 'level1/index');
        $this->assertEquals('/level1', $entry->url());
    }

    public function testContent()
    {
        $html = "<p><strong>Lorem ipsum</strong> <em>dolor</em> sit amet.</p>";

        $this->assertEquals($html, $this->entry->content());
    }

    public function testExcerpt()
    {
        $entry   = $this->entryManager->getEntry('example-post-2');
        $excerpt = "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque elementum libero eu feugiat sodales. Praesent sed tortor libero. Etiam aliquet, velit auctor accumsan maximus, orci mi rutrum libero, ut pharetra lorem velit nec sem. Ut non luctus mauris, ut bibendum nibh. Ut mi nisl, porta euismod&hellip;";

        $this->assertEquals($excerpt, $entry->excerpt());
    }
}
