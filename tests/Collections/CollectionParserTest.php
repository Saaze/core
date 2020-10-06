<?php

namespace Saaze\Tests\Collections;

use Saaze\Interfaces\CollectionParserInterface;
use Saaze\Tests\TestCase;

class CollectionParserTest extends TestCase
{
    /**
     * @var CollectionParserInterface
     */
    protected $collectionParser;

    public function setUp(): void
    {
        parent::setUp();

        $this->collectionParser = $this->container->get(CollectionParserInterface::class);
    }

    public function testParseCollection()
    {
        $collection = $this->collectionParser->parseCollection(SAAZE_CONTENT_PATH . '/pages.yml');

        $this->assertIsArray($collection);
        $this->assertArrayHasKey('title', $collection);
        $this->assertArrayHasKey('sort', $collection);
        $this->assertArrayHasKey('index_route', $collection);
        $this->assertArrayHasKey('entry_route', $collection);
        $this->assertIsArray($collection['sort']);
        $this->assertEquals('Pages', $collection['title']);
    }
}
