<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Tests;

use Bogosoft\Maniple\ClassActivator;
use Bogosoft\Maniple\NullParameterResolver;
use Bogosoft\Maniple\TypedParameterResolver;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ClassActivatorTest extends TestCase
{
    function testCanActivateService(): void
    {
        $expected = 1200;

        $engine = new Engine($expected);

        $container = new SingleRegistrationContainer(Engine::class, $engine);

        $resolver = new TypedParameterResolver();

        $activator = new ClassActivator(Car::class, $resolver);

        $car = $activator->activate($container);

        $this->assertInstanceOf(Car::class, $car);

        $this->assertEquals($expected, $car->getEngine()->getSize());
    }

    function testThrowsRuntimeExceptionWhenConstructorParametersCannotBeResolved(): void
    {
        $container = new EmptyContainer();

        $resolver = new NullParameterResolver();

        $activator = new ClassActivator(Car::class, $resolver);

        $this->expectException(RuntimeException::class);

        $activator->activate($container);
    }
}
