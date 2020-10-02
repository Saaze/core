<?php

namespace Saaze;

use Saaze\Container\Container;

class Saaze
{
    /**
     * @return void
     */
    public function run()
    {
        $container = Container::getInstance();

        $container->call([\Saaze\Interfaces\RouterInterface::class, 'handle']);
    }
}
