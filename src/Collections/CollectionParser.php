<?php

namespace Saaze\Collections;

use Symfony\Component\Yaml\Yaml;
use Saaze\Interfaces\CollectionParserInterface;

class CollectionParser implements CollectionParserInterface
{
    /**
     * @param string $filePath
     * @return array
     */
    public function parseCollection($filePath)
    {
        return Yaml::parse(file_get_contents($filePath));
    }
}
