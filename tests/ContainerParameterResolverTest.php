<?php

declare(strict_types=1);

namespace Tests;

use Bogosoft\Maniple\ContainerParameterResolver;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface as IContainer;
use ReflectionFunction;

class ContainerParameterResolverTest extends TestCase
{
    function testContainerResolverCannotResolveWhenParameterIsNotContainerInterface(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $rf = new ReflectionFunction(function(string $id): void
        {
        });

        $rp = $rf->getParameters()[0];

        $resolver = new ContainerParameterResolver();

        $container = new EmptyContainer();

        $this->assertFalse($resolver->resolve($rp, $container, $result));
    }

    function testContainerResolverCanResolveWhenParameterIsContainerInterface(): void
    {
        /** @noinspection PhpUnhandledExceptionInspection */
        $rf = new ReflectionFunction(function(IContainer $services): IContainer
        {
            return $services;
        });

        $rp = $rf->getParameters()[0];

        $resolver = new ContainerParameterResolver();

        $container = new EmptyContainer();

        $this->assertTrue($resolver->resolve($rp, $container, $result));

        $this->assertEquals($container, $result);
    }
}
