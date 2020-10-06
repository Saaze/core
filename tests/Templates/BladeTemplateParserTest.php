<?php

namespace Saaze\Tests\Templates;

use Saaze\Templates\BladeTemplateParser;
use Saaze\Tests\TestCase;

class BladeTemplateParserTest extends TestCase
{
    /**
     * @var BladeTemplateParser
     */
    protected $templateParser;

    public function setUp(): void
    {
        parent::setUp();

        $this->templateParser = $this->container->get(BladeTemplateParser::class);
    }

    public function testRender()
    {
        $html = $this->templateParser->render('error', [
            'code'    => '404',
            'message' => 'Not Found'
        ]);

        $this->assertStringContainsString('<h1>404</h1>', $html);
        $this->assertStringContainsString('<div>Not Found</div>', $html);
    }

    public function testTemplateExists()
    {
        $this->assertTrue($this->templateParser->templateExists('collection'));
        $this->assertTrue($this->templateParser->templateExists('entry'));
        $this->assertFalse($this->templateParser->templateExists('nonexistent'));
    }
}
