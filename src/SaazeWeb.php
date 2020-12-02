<?php

namespace Saaze;

class SaazeWeb extends Saaze
{
    /**
     * @return void
     */
    public function run()
    {
        $this->bootstrap();
        $this->app->run();
    }
}
