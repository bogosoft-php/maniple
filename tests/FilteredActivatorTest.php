<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Maniple\FilteredActivator;
use Bogosoft\Maniple\IActivator;
use Bogosoft\Maniple\InstanceActivator;
use Bogosoft\Maniple\IServiceFilter;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface as IContainer;

class FilteredActivatorTest extends TestCase
{
    function testCallsActivatorWhenNoFiltersPresent(): void
    {
        $expected = rand(600, 2400);

        $engine = new Engine($expected);

        $activator = new InstanceActivator($engine);
        $activator = new FilteredActivator($activator);

        $engine = $activator->activate(new EmptyContainer());

        $this->assertEquals($expected, $engine->getSize());
    }

    function testCanShortCircuitActivationWithFilter(): void
    {
        $large = 1800;
        $small = 600;

        $engine = new Engine($large);

        $activator = new InstanceActivator($engine);

        $engine = $activator->activate(new EmptyContainer());

        $this->assertEquals($large, $engine->getSize());

        $filter = new class($small) implements IServiceFilter
        {
            private int $size;

            function __construct(int $size)
            {
                $this->size = $size;
            }

            function filter(IContainer $container, IActivator $activator)
            {
                return new Engine($this->size);
            }
        };

        $activator = new FilteredActivator($activator, $filter);

        $engine = $activator->activate(new EmptyContainer());

        $this->assertEquals($small, $engine->getSize());
    }
}
