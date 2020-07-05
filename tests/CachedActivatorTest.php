<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Maniple\CachedActivator;
use Bogosoft\Maniple\FactoryActivator;
use Bogosoft\Maniple\TypedParameterResolver;
use PHPUnit\Framework\TestCase;

class CachedActivatorTest extends TestCase
{
    function testCallsUnderlyingActivatorOnlyOnce(): void
    {
        $calls = 0;

        $factory = function(Engine $engine) use (&$calls): Car
        {
            ++$calls;

            return new Car($engine);
        };

        $resolvers = [new TypedParameterResolver()];

        $activator = new FactoryActivator($factory, $resolvers);
        $activator = new CachedActivator($activator);

        $expected = 1399;

        $engine = new Engine($expected);

        $container = new SingleRegistrationContainer(Engine::class, $engine);

        /** @var Car $car */
        $car = null;

        for ($i = 0; $i < 16; $i++)
            $car = $activator->activate($container);

        $this->assertInstanceOf(Car::class, $car);

        $this->assertEquals($expected, $car->getEngine()->getSize());

        $this->assertEquals(1, $calls);
    }
}
