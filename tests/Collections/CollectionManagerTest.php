<?php

namespace Saaze\Tests\Collections;

use Saaze\Interfaces\CollectionManagerInterface;
use Saaze\Tests\TestCase;

class CollectionManagerTest extends TestCase
{
    /**
     * @var CollectionManagerInterface
     */
    protected $collectionManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->collectionManager = $this->container->get(CollectionManagerInterface::class);
    }

    public function testGetCollections()
    {
        $collections = $this->collectionManager->getCollections();

        $this->assertIsArray($collections);
        $this->assertArrayHasKey('pages', $collections);
        $this->assertArrayHasKey('posts', $collections);
        $this->assertCount(3, $collections);
    }

    public function testGetCollectionsIsSorted()
    {
        $collections = $this->collectionManager->getCollections();
        $keys = array_keys($collections);

        $this->assertEquals('docs', $keys[0]);
        $this->assertEquals('posts', $keys[1]);
        $this->assertEquals('pages', $keys[2]);
    }

    public function testGetCollection()
    {
        $collection = $this->collectionManager->getCollection('pages');
        $this->assertInstanceOf(\Saaze\Interfaces\CollectionInterface::class, $collection);

        $collection = $this->collectionManager->getCollection('nonexistent');
        $this->assertNull($collection);
    }
}
