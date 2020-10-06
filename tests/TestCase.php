<?php

namespace Saaze\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;

class TestCase extends PHPUnitTestCase
{
    /**
     * @var \DI\Container
     */
    protected $container;

    public function setUp(): void
    {
        $this->container = container();
    }
}
