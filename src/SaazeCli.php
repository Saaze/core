<?php

namespace Saaze;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class SaazeCli extends Saaze
{
    /**
     * @return void
     */
    public function run()
    {
        $this->bootstrap();

        $kernel = $this->app->make(
            'Illuminate\Contracts\Console\Kernel'
        );

        exit($kernel->handle(new ArgvInput, new ConsoleOutput));
    }
}
