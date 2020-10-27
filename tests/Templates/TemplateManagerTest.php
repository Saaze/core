<?php

namespace Saaze\Tests\Templates;

use Saaze\Tests\TestCase;
use Saaze\Interfaces\CollectionInterface;
use Saaze\Interfaces\EntryManagerInterface;
use Saaze\Interfaces\TemplateManagerInterface;
use Saaze\Interfaces\CollectionManagerInterface;

class TemplateManagerTest extends TestCase
{
    /**
     * @var CollectionInterface
     */
    protected $collection;

    /**
     * @var TemplateManagerInterface
     */
    protected $templateManager;

    public function setUp(): void
    {
        parent::setUp();

        $collectionManager     = $this->container->get(CollectionManagerInterface::class);
        $this->collection      = $collectionManager->getCollection('posts');
        $this->templateManager = $this->container->get(TemplateManagerInterface::class);
    }

    public function testRenderCollection()
    {
        $html = $this->templateManager->renderCollection($this->collection, 1);

        $this->assertStringContainsString('<h2><a href="/blog/example-post-1">Hello World 1</a></h2>', $html);
        $this->assertStringContainsString('<a href="/blog/page/2">&larr; Older</a>', $html);
        $this->assertStringNotContainsString('Hello World 11', $html);
        $this->assertStringNotContainsString('Newer &rarr;', $html);

        $html = $this->templateManager->renderCollection($this->collection, 2);

        $this->assertStringContainsString('<h2><a href="/blog/example-post-11">Hello World 11</a></h2>', $html);
        $this->assertStringContainsString('<a href="/blog/page/1">Newer &rarr;</a>', $html);
        $this->assertStringNotContainsString('Hello World 5', $html);
        $this->assertStringNotContainsString('&larr; Older', $html);
    }

    public function testRenderEntry()
    {
        $entryManager = $this->container->get(EntryManagerInterface::class);
        $entryManager->setCollection($this->collection);
        $entry = $entryManager->getEntry('example-post-1');

        $html = $this->templateManager->renderEntry($entry);

        $this->assertStringContainsString('<h1>Hello World 1</h1>', $html);
        $this->assertStringContainsString('<p><strong>Lorem ipsum</strong> <em>dolor</em> sit amet.</p>', $html);
    }

    public function testRenderError()
    {
        $html = $this->templateManager->renderError('Not Found', 404);

        $this->assertStringContainsString('<h1>404</h1>', $html);
        $this->assertStringContainsString('<div>Not Found</div>', $html);
    }
}
