<?php

namespace Saaze\Tests\Collections;

use Saaze\Interfaces\CollectionManagerInterface;
use Saaze\Tests\TestCase;

class CollectionTest extends TestCase
{
    /**
     * @var CollectionManagerInterface
     */
    protected $collectionManager;

    /**
     * @var Collection
     */
    protected $collection;

    public function setUp(): void
    {
        parent::setUp();

        $this->collectionManager = $this->container->get(CollectionManagerInterface::class);
        $this->collection        = $this->collectionManager->getCollection('pages');
    }

    public function testData()
    {
        $data = $this->collection->data();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('title', $data);
        $this->assertArrayHasKey('sort', $data);
        $this->assertArrayHasKey('index_route', $data);
        $this->assertArrayHasKey('entry_route', $data);
    }

    public function testSlug()
    {
        $this->assertEquals('pages', $this->collection->slug());
    }

    public function testIndexRoute()
    {
        $this->assertEquals('/', $this->collection->indexRoute());
    }

    public function testEntryRoute()
    {
        $this->assertEquals('/{slug}', $this->collection->entryRoute());
    }

    public function testIndexIsEntry()
    {
        $this->assertTrue($this->collection->indexIsEntry());

        $collection = $this->collectionManager->getCollection('posts');
        $this->assertFalse($collection->indexIsEntry());
    }
}
