<?php

namespace Saaze\Entries;

use Symfony\Component\Yaml\Yaml;
use Saaze\Interfaces\EntryParserInterface;

class EntryParser implements EntryParserInterface
{
    /**
     * @param string $filePath
     * @return array
     */
    public function parseEntry($filePath)
    {
        $content = file_get_contents($filePath);
        $parts    = explode('---', $content, 2);

        $data = Yaml::parse($parts[0]);
        $data['content_raw'] = $parts[1] ?? '';

        return $data;
    }
}
