<?php

namespace Saaze\Interfaces;

interface EntryParserInterface
{
    /**
     * Parse an entry
     *
     * @param string $filePath
     * @return array
     */
    public function parseEntry($filePath);
}
