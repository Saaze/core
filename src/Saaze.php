<?php

namespace Saaze;

use Dotenv\Dotenv;

class Saaze
{
    /**
     * @param string $saazePath
     */
    public function __construct($saazePath)
    {
        define('SAAZE_PATH', $saazePath);

        $dotenv = Dotenv::createImmutable($saazePath);
        $dotenv->load();
    }

    /**
     * @return void
     */
    public function run()
    {
        container()->call([\Saaze\Interfaces\RouterInterface::class, 'handle']);
    }
}
