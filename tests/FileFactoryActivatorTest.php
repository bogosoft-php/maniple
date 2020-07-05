<?php /** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Tests;

use Bogosoft\Maniple\FileFactoryActivator;
use Bogosoft\Maniple\TypedParameterResolver;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class FileFactoryActivatorTest extends TestCase
{
    function testCanActivateService(): void
    {
        $path = __DIR__ . '/services/car.php';

        $resolvers = [new TypedParameterResolver()];

        $expected = 899;

        $engine = new Engine($expected);

        $container = new SingleRegistrationContainer(Engine::class, $engine);

        $activator = new FileFactoryActivator($path, $resolvers);

        $car = $activator->activate($container);

        $this->assertInstanceOf(Car::class, $car);

        $this->assertEquals($expected, $car->getEngine()->getSize());
    }

    function testThrowsRuntimeExceptionWhenFileDoesNotExist(): void
    {
        $path = 'not-a-file.php';

        $this->assertFalse(is_file($path));

        $activator = new FileFactoryActivator($path, []);

        $this->expectException(RuntimeException::class);

        $activator->activate(new EmptyContainer());
    }

    function testThrowsRuntimeExceptionWhenParametersCannotBeResolved(): void
    {
        $path = __DIR__ . '/services/car.php';

        $this->assertTrue(is_file($path));

        $activator = new FileFactoryActivator($path, []);

        $this->expectException(RuntimeException::class);

        $activator->activate(new EmptyContainer());
    }
}
