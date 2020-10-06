<?php

namespace Saaze;


class Saaze
{
    /**
     * @return void
     */
    public function run()
    {
        container()->call([\Saaze\Interfaces\RouterInterface::class, 'handle']);
    }
}
