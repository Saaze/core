<?php

namespace Saaze\Tests\Entries;

use Saaze\Interfaces\EntryParserInterface;
use Saaze\Tests\TestCase;

class EntryParserTest extends TestCase
{
    /**
     * @var EntryParserInterface
     */
    protected $entryParser;

    public function setUp(): void
    {
        parent::setUp();

        $this->entryParser = $this->container->get(EntryParserInterface::class);
    }

    public function testParseCollection()
    {
        $entry = $this->entryParser->parseEntry(SAAZE_CONTENT_PATH . '/pages/index.md');

        $this->assertIsArray($entry);
        $this->assertArrayHasKey('title', $entry);
        $this->assertArrayHasKey('content_raw', $entry);
        $this->assertEquals('Home', $entry['title']);
    }
}
