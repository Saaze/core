<?php

namespace Saaze;

use Dotenv\Dotenv;
use Symfony\Component\Console\Application;

class SaazeCli
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

    public function run()
    {
        $container = container();

        $app = new Application('Saaze');
        $app->add($container->get(\Saaze\Commands\BuildCommand::class));
        $app->add($container->get(\Saaze\Commands\ServeCommand::class));
        $app->add($container->get(\Saaze\Commands\Make\MakeCollectionCommand::class));
        $app->run();
    }
}
