<?php /** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Tests;

use Bogosoft\Maniple\FactoryActivator;
use Bogosoft\Maniple\TypedParameterResolver;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FactoryActivatorTest extends TestCase
{
    function testCanActivateService(): void
    {
        $factory = function(Engine $engine): Car
        {
            return new Car($engine);
        };

        $expected = 1600;

        $engine = new Engine($expected);

        $container = new SingleRegistrationContainer(Engine::class, $engine);

        $resolvers = [new TypedParameterResolver()];

        $activator = new FactoryActivator($factory, $resolvers);

        $car = $activator->activate($container);

        $this->assertInstanceOf(Car::class, $car);

        $this->assertEquals($expected, $car->getEngine()->getSize());
    }

    function testThrowsRuntimeExceptionWhenParametersCannotBeResolved(): void
    {
        $factory = function(Engine $engine): Car
        {
            return new Car($engine);
        };

        $container = new EmptyContainer();

        $activator = new FactoryActivator($factory, []);

        $this->expectException(RuntimeException::class);

        $activator->activate($container);
    }
}