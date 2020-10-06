<?php

namespace Saaze\Tests\Content;

use Saaze\Content\MarkdownContentParser;
use Saaze\Tests\TestCase;

class MarkdownContentParserTest extends TestCase
{
    /**
     * @var MarkdownContentParser
     */
    protected $contentParser;

    public function setUp(): void
    {
        parent::setUp();

        $this->contentParser = $this->container->get(MarkdownContentParser::class);
    }

    public function testToHtml()
    {
        $html = "<p><strong>Lorem ipsum</strong> <em>dolor</em> sit amet.</p>";

        $this->assertEquals($html, $this->contentParser->toHtml('**Lorem ipsum** _dolor_ sit amet.'));
    }
}
