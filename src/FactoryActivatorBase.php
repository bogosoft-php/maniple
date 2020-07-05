<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

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
    /** @var IParameterResolver[] */
    private array $resolvers;

    /**
     * Create a new factory activator.
     *
     * @param IParameterResolver[] $resolvers An array of parameter resolvers.
     */
    protected function __construct(array $resolvers)
    {
        $this->resolvers = $resolvers;
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
        /** @var IParameterResolver $resolver */
        foreach ($this->resolvers as $resolver)
            if ($resolver->resolve($rp, $services, $result))
                return $result;

        $message = sprintf($this->getErrorMessageFormat(), $rp->getName());

        throw new RuntimeException($message);
    }
}
