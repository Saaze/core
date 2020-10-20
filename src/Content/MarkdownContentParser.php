<?php

namespace Saaze\Content;

use ParsedownExtra;
use Saaze\Interfaces\ContentParserInterface;

class MarkdownContentParser implements ContentParserInterface
{
    /**
     * @param string $content
     * @return string
     */
    public function toHtml($content)
    {
        return ParsedownExtra::instance()
            ->setBreaksEnabled(true)
            ->text($content);
    }
}
