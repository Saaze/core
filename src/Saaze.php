<?php

namespace Saaze;

use Dotenv\Dotenv;
use Saaze\Container\Container;

class Saaze
{
    /**
     * @param string $saazePath
     */
    public function __construct($saazePath)
    {
        define('SAAZE_PATH', $saazePath);

        if (file_exists("{$saazePath}/.env")) {
            $dotenv = Dotenv::createImmutable($saazePath);
            $dotenv->load();
        }
    }

    /**
     * @return void
     */
    public function run()
    {
        Container::bootProviders();
        container()->call([\Saaze\Interfaces\RouterInterface::class, 'handle']);
    }
}
