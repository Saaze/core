<?php

namespace Saaze\Entries;

use Saaze\Interfaces\EntryParserInterface;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class EntryParser implements EntryParserInterface
{
    /**
     * @param string $filePath
     * @return array
     */
    public function parseEntry($filePath)
    {
        $object = YamlFrontMatter::parse(file_get_contents($filePath));

        $data = $object->matter();
        $data['content_raw'] = $object->body();

        return $data;
    }
}
