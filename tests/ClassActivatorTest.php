<?php /** @noinspection PhpUnhandledExceptionInspection */

namespace Bogosoft\Maniple\Tests;

use Bogosoft\Maniple\ClassActivator;
use Bogosoft\Reflection\NullResolver;
use Bogosoft\Reflection\TypedResolver;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ClassActivatorTest extends TestCase
{
    function testCanActivateService(): void
    {
        $expected = 1200;

        $engine = new Engine($expected);

        $container = new SingleRegistrationContainer(Engine::class, $engine);

        $resolver = new TypedResolver();

        $activator = new ClassActivator(Car::class, $resolver);

        $car = $activator->activate($container);

        $this->assertInstanceOf(Car::class, $car);

        $this->assertEquals($expected, $car->getEngine()->getSize());
    }

    function testThrowsRuntimeExceptionWhenConstructorParametersCannotBeResolved(): void
    {
        $container = new EmptyContainer();

        $resolver = new NullResolver();

        $activator = new ClassActivator(Car::class, $resolver);

        $this->expectException(RuntimeException::class);

        $activator->activate($container);
    }
}
