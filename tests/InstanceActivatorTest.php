<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Maniple\InstanceActivator;
use PHPUnit\Framework\TestCase;

class InstanceActivatorTest extends TestCase
{
    function testCanActivateService(): void
    {
        $expected = 600;

        $engine = new Engine($expected);

        $activator = new InstanceActivator($engine);

        $service = $activator->activate(new EmptyContainer());

        $this->assertInstanceOf(Engine::class, $engine);

        $this->assertEquals($expected, $service->getSize());
    }
}
