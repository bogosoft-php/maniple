<?php /** @noinspection PhpUnhandledExceptionInspection */

declare(strict_types=1);

namespace Tests;

use Bogosoft\Maniple\TypedPropertyResolver;
use Bogosoft\Maniple\ValueObjectActivator;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface as IContainer;

class ValueObjectActivatorTest extends TestCase
{
    function testCanActivateService(): void
    {
        $expectedAge  = rand(25, 45);
        $expectedName = 'Alice';

        $resolver = new TypedPropertyResolver();

        $activator = new ValueObjectActivator(Person::class, $resolver);

        $container = new class($expectedAge, $expectedName) implements IContainer
        {
            private int $age;
            private string $name;

            function __construct(int $age, string $name)
            {
                $this->age  = $age;
                $this->name = $name;
            }

            /**
             * @inheritDoc
             */
            public function get($id)
            {
                switch ($id)
                {
                    case 'int':
                        return $this->age;
                    case 'string':
                        return $this->name;
                    default:
                        return null;
                }
            }

            /**
             * @inheritDoc
             */
            public function has($id)
            {
                return in_array($id, ['int', 'string']);
            }
        };

        $person = $activator->activate($container);

        $this->assertInstanceOf(Person::class, $person);

        $this->assertEquals($expectedAge, $person->age);
        $this->assertEquals($expectedName, $person->name);
    }
}
