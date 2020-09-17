<?php

namespace Saaze;

use Saaze\Commands\BuildCommand;
use Saaze\Commands\ServeCommand;
use Symfony\Component\Console\Application;

class SaazeCli
{
    public function run()
    {
        $app = new Application('Saaze');
        $app->add(new BuildCommand);
        $app->add(new ServeCommand);
        $app->run();
    }
}
