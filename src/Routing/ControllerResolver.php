<?php

namespace Saaze\Routing;

use Symfony\Component\HttpKernel\Controller\ControllerResolver as SymfonyControllerResolver;

class ControllerResolver extends SymfonyControllerResolver
{
    /**
     * Returns an instantiated controller.
     *
     * @return object
     */
    protected function instantiateController(string $class)
    {
        // Enable dependency injection for controllers
        return container()->make($class);
    }
}
