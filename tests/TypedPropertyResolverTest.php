<?php /** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Tests;

use Bogosoft\Maniple\TypedPropertyResolver;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface as IContainer;
use ReflectionClass;

class TypedPropertyResolverTest extends TestCase
{
    function testCannotResolveWhenPropertyIsNotTyped(): void
    {
        $test = new class
        {
            /** @var mixed */
            public $greeting;
        };

        $rc = new ReflectionClass($test);

        $rp = $rc->getProperties()[0];

        $container = new class implements IContainer
        {
            public function get($id)
            {
                return 'Hello, World!';
            }

            public function has($id)
            {
                return true;
            }
        };

        $resolver = new TypedPropertyResolver();

        $this->assertFalse($resolver->resolve($rp, $container, $result));
    }

    function testCannotResolveWhenParameterTypeIsNotRegistered(): void
    {
        $test = new class
        {
            public string $greeting;
        };

        $rc = new ReflectionClass($test);

        $rp = $rc->getProperties()[0];

        $container = new EmptyContainer();

        $resolver = new TypedPropertyResolver();

        $this->assertFalse($resolver->resolve($rp, $container, $result));
    }

    function testCanResolveWhenParameterIsTypedAndRegistered(): void
    {
        $expected = 'Hello, World!';

        $test = new class
        {
            public string $greeting;
        };

        $rc = new ReflectionClass($test);
        
        $rp = $rc->getProperties()[0];

        $container = new SingleRegistrationContainer('string', $expected);

        $resolver = new TypedPropertyResolver();

        $this->assertTrue($resolver->resolve($rp, $container, $actual));

        $this->assertEquals($expected, $actual);
    }
}
