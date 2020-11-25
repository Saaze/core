<?php

namespace Saaze\Tests\Container;

use Saaze\Tests\TestCase;

class ContainerTest extends TestCase
{
    public function testContainerLoadsSaazeServiceProvider()
    {
        $providers = $this->container->get('providers');

        $this->assertTrue(in_array(\Saaze\Providers\SaazeServiceProvider::class, $this->providerClasses($providers)));
        $this->assertTrue($this->container->has('path.base'));
        $this->assertTrue($this->container->has('path.cache'));
        $this->assertTrue($this->container->has('path.content'));
        $this->assertTrue($this->container->has('path.public'));
        $this->assertTrue($this->container->has('path.templates'));
        $this->assertTrue($this->container->has('config.entries_per_page'));
        $this->assertEquals(SAAZE_PATH, $this->container->get('path.base'));
    }

    public function testContainerLoadsCustomServiceProvider()
    {
        $providers = $this->container->get('providers');

        $this->assertTrue(in_array('CustomProvider', $this->providerClasses($providers)));
        $this->assertTrue($this->container->has('config.custom_value'));
        $this->assertEquals('foo', $this->container->get('config.custom_value'));

        $routes = $this->container->get('routes');
        $this->assertTrue(in_array('/custom-route', $this->routePaths($routes)));
    }

    /**
     * @param array $providers
     * @return array
     */
    private function providerClasses($providers)
    {
        return array_map(function ($provider) {
            return get_class($provider);
        }, $providers);
    }

    /**
     * @param array $routes
     * @return array
     */
    private function routePaths($routes)
    {
        return array_map(function ($route) {
            return $route['path'];
        }, $routes);
    }
}
