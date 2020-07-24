<?php /** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Bogosoft\Maniple\Tests;

use Bogosoft\Maniple\FactoryActivator;
use Bogosoft\Reflection\NullResolver;
use Bogosoft\Reflection\TypedResolver;
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

        $resolver = new TypedResolver();

        $activator = new FactoryActivator($factory, $resolver);

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

        $resolver = new NullResolver();

        $activator = new FactoryActivator($factory, $resolver);

        $this->expectException(RuntimeException::class);

        $activator->activate($container);
    }
}
