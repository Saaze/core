<?php

namespace Saaze\Interfaces;

interface CollectionParserInterface
{
    /**
     * Parse a collection
     *
     * @param string $filePath
     * @return array
     */
    public function parseCollection($filePath);
}
