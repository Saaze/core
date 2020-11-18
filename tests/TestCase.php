<?php

namespace Saaze\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Saaze\Container\Container;

class TestCase extends PHPUnitTestCase
{
    /**
     * @var \DI\Container
     */
    protected $container;

    public function setUp(): void
    {
        Container::bootProviders();
        $this->container = container();
    }
}
