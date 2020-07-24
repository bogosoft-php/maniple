<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Bogosoft\Reflection\IParameterResolver;
use Psr\Container\ContainerInterface as IContainer;
use ReflectionException;
use ReflectionFunction;
use ReflectionParameter;
use RuntimeException;

/**
 * A partial implementation of the {@see IActivator} contract that relies on
 * a {@see callable} object for activating a service.
 *
 * @package Bogosoft\Maniple
 */
abstract class FactoryActivatorBase implements IActivator
{
    private IParameterResolver $resolver;

    /**
     * Create a new factory activator.
     *
     * @param IParameterResolver $resolver A parameter resolver.
     */
    protected function __construct(IParameterResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException when the given callable cannot be
     *                             reflected upon.
     */
    function activate(IContainer $services)
    {
        $factory = $this->getFactory();

        $rf = new ReflectionFunction($factory);

        $args = [];

        foreach ($rf->getParameters() as $rp)
            $args[] = $this->resolve($rp, $services);

        return $rf->invokeArgs($args);
    }

    /**
     * Get a factory as an invokable object.
     *
     * @return callable An object responsible for activating a service.
     */
    protected abstract function getFactory(): callable;

    /**
     * Get an error message format.
     *
     * @return string An error message format.
     */
    protected function getErrorMessageFormat(): string
    {
        return 'Unresolvable parameter: \'%s\'.';
    }

    private function resolve(ReflectionParameter $rp, IContainer $services)
    {
        if ($this->resolver->resolveParameter($rp, $services, $result))
            return $result;

        $message = sprintf($this->getErrorMessageFormat(), $rp->getName());

        throw new RuntimeException($message);
    }
}
