<?php

namespace Saaze\Tests;

use Saaze\Container\Container;
use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    /**
     * @var \DI\Container
     */
    protected $container;

    public function setUp(): void
    {
        $this->container = Container::getInstance();
    }
}
