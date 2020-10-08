<?php

namespace Saaze\Commands\Make;

use Symfony\Component\Console\Command\Command;

class MakeCommand extends Command
{
    /**
     * Sanitize a string for use as a filename
     *
     * @param string $string
     * @return string
     */
    protected function sanitizeFilename($string)
    {
        $string = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($string));
        $string = preg_replace('/[-]+/', '-', $string);

        return $string;
    }
}
