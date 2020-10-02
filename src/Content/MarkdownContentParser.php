<?php

namespace Saaze\Content;

use Parsedown;
use Saaze\Interfaces\ContentParserInterface;

class MarkdownContentParser implements ContentParserInterface
{
    /**
     * @param string $content
     * @return string
     */
    public function toHtml($content)
    {
        return Parsedown::instance()
            ->setBreaksEnabled(true)
            ->text($content);
    }
}
