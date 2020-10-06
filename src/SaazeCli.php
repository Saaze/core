<?php

namespace Saaze;

use Dotenv\Dotenv;
use Saaze\Commands\BuildCommand;
use Saaze\Commands\ServeCommand;
use Symfony\Component\Console\Application;

class SaazeCli
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

    public function run()
    {
        $container = container();

        $app = new Application('Saaze');
        $app->add($container->get(BuildCommand::class));
        $app->add($container->get(ServeCommand::class));
        $app->run();
    }
}
