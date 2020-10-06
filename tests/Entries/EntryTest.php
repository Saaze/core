<?php

namespace Saaze\Tests\Entries;

use Saaze\Tests\TestCase;
use Saaze\Interfaces\CollectionManagerInterface;
use Saaze\Interfaces\EntryManagerInterface;

class EntryTest extends TestCase
{
    /**
     * @var EntryManagerInterface
     */
    protected $entryManager;

    /**
     * @var Entry
     */
    protected $entry;

    public function setUp(): void
    {
        parent::setUp();

        $collectionManager  = $this->container->get(CollectionManagerInterface::class);
        $collection         = $collectionManager->getCollection('posts');
        $this->entryManager = $this->container->get(EntryManagerInterface::class);
        $this->entryManager->setCollection($collection);
        $this->entry        = $this->entryManager->getEntry('example-post-1');
        $this->entry->setCollection($collection);
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
    }

    public function testUrl()
    {
        $this->assertEquals('/blog/example-post-1', $this->entry->url());
    }

    public function testUrlIsIndex()
    {
        $collectionManager = $this->container->get(CollectionManagerInterface::class);
        $collection        = $collectionManager->getCollection('docs');
        $entryManager      = $this->container->get(EntryManagerInterface::class);
        $entryManager->setCollection($collection);
        $entry             = $entryManager->getEntry('index');
        $entry->setCollection($collection);

        $this->assertEquals($collection->indexRoute(), $entry->url());
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
