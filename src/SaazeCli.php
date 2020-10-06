<?php

namespace Saaze;

use Saaze\Commands\BuildCommand;
use Saaze\Commands\ServeCommand;
use Symfony\Component\Console\Application;

class SaazeCli
{
    public function run()
    {
        $container = container();

        $app = new Application('Saaze');
        $app->add($container->get(BuildCommand::class));
        $app->add($container->get(ServeCommand::class));
        $app->run();
    }
}
