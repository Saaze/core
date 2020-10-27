<?php

namespace Saaze\Tests\Collections;

use Saaze\Interfaces\CollectionManagerInterface;
use Saaze\Interfaces\EntryManagerInterface;
use Saaze\Tests\TestCase;

class EntryManagerTest extends TestCase
{
    /**
     * @var \Saaze\Interfaces\CollectionInterface
     */
    protected $collection;

    /**
     * @var EntryManagerInterface
     */
    protected $entryManager;

    public function setUp(): void
    {
        parent::setUp();

        $this->entryManager = $this->getEntryManager('posts');
    }

    /**
     * @param string $collectionSlug
     * @return EntryManagerInterface
     */
    private function getEntryManager($collectionSlug)
    {
        $collectionManager = $this->container->get(CollectionManagerInterface::class);
        $collection        = $collectionManager->getCollection($collectionSlug);
        $entryManager      = $this->container->get(EntryManagerInterface::class);
        $entryManager->setCollection($collection);

        return $entryManager;
    }

    public function testGetEntries()
    {
        $entries = $this->entryManager->getEntries();

        $this->assertIsArray($entries);
        $this->assertCount(20, $entries);
    }

    public function testGetEntry()
    {
        $entry = $this->entryManager->getEntry('example-post-1');
        $this->assertInstanceOf(\Saaze\Interfaces\EntryInterface::class, $entry);

        $entry = $this->entryManager->getEntry('nonexistent');
        $this->assertNull($entry);

        $entryManager = $this->getEntryManager('pages');
        $this->assertNotNull($entryManager->getEntry('index'));
        $this->assertNotNull($entryManager->getEntry('level1'));
        $this->assertNotNull($entryManager->getEntry('level1/page'));
        $this->assertNotNull($entryManager->getEntry('level1/level2/page'));

        $entryManager = $this->getEntryManager('docs');
        $this->assertNotNull($entryManager->getEntry('index'));
        $this->assertNotNull($entryManager->getEntry('level1'));
        $this->assertNotNull($entryManager->getEntry('level1/page'));
        $this->assertNotNull($entryManager->getEntry('level1/level2/page'));
    }

    public function testGetEntriesSortedByDate()
    {
        $entries   = $this->entryManager->getEntries();
        $sortField = 'date';

        $this->assertTrue(strtotime($entries[0]->data($sortField)) > strtotime($entries[1]->data($sortField)));
        $this->assertTrue(strtotime($entries[1]->data($sortField)) > strtotime($entries[2]->data($sortField)));
    }

    public function testGetEntriesSortedMissingSortField()
    {
        $entryManager = $this->getEntryManager('docs');
        $entries      = $entryManager->getEntries();

        $this->assertEquals('Order Missing', $entries[0]->data('title'));
    }

    public function testGetEntriesForTemplate()
    {
        $entries = $this->entryManager->getEntriesForTemplate();

        $this->assertIsArray($entries);
        $this->assertEquals(20, count($entries));
    }

    public function testPaginateEntriesForTemplate()
    {
        $entries    = $this->entryManager->getEntriesForTemplate();
        $pagination = $this->entryManager->paginateEntriesForTemplate($entries, 1, 10);

        $this->assertEquals(1, $pagination['currentPage']);
        $this->assertEquals(1, $pagination['prevPage']);
        $this->assertEquals(2, $pagination['nextPage']);
        $this->assertEquals('', $pagination['prevUrl']);
        $this->assertEquals('/blog/page/2', $pagination['nextUrl']);
        $this->assertEquals(10, $pagination['perPage']);
        $this->assertEquals(20, $pagination['totalEntries']);
        $this->assertEquals(2, $pagination['totalPages']);
        $this->assertIsArray($pagination['entries']);
        $this->assertEquals(10, count($pagination['entries']));

        $pagination = $this->entryManager->paginateEntriesForTemplate($entries, 3, 5);

        $this->assertEquals(3, $pagination['currentPage']);
        $this->assertEquals(2, $pagination['prevPage']);
        $this->assertEquals(4, $pagination['nextPage']);
        $this->assertEquals('/blog/page/2', $pagination['prevUrl']);
        $this->assertEquals('/blog/page/4', $pagination['nextUrl']);
        $this->assertEquals(5, $pagination['perPage']);
        $this->assertEquals(20, $pagination['totalEntries']);
        $this->assertEquals(4, $pagination['totalPages']);
        $this->assertIsArray($pagination['entries']);
        $this->assertEquals(5, count($pagination['entries']));
    }

    public function testGetEntryForTemplate()
    {
        $entries = $this->entryManager->getEntries();
        $entry = $this->entryManager->getEntryForTemplate($entries[0]);

        $this->assertArrayHasKey('title', $entry);
        $this->assertArrayHasKey('date', $entry);
        $this->assertArrayHasKey('content_raw', $entry);
        $this->assertArrayHasKey('content', $entry);
        $this->assertArrayHasKey('url', $entry);
        $this->assertArrayHasKey('excerpt', $entry);
        $this->assertArrayHasKey('title', $entry);
    }
}
