<?php

namespace Saaze\Interfaces;

interface ContentParserInterface
{
    /**
     * Parse raw content and return HTML
     *
     * @param string $content
     * @return string
     */
    public function toHtml($content);
}
