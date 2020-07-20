<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Psr\Container\ContainerInterface as IContainer;
use ReflectionParameter;

/**
 * A composite implementation of the {@see IParameterResolver} contract
 * that allows multiple parameter resolvers to behave as if they were a
 * single parameter resolver.
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Maniple
 */
final class CompositeParameterResolver implements IParameterResolver
{
    /** @var IParameterResolver[]  */
    private array $resolvers;

    /**
     * Create a new composite parameter resolver.
     *
     * @param IParameterResolver ...$resolvers Zero or more parameter
     *                                         resolvers from which the
     *                                         composite will be formed.
     */
    function __construct(IParameterResolver ...$resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * @inheritDoc
     */
    function resolve(ReflectionParameter $rp, IContainer $services, &$result): bool
    {
        foreach ($this->resolvers as $resolver)
            if ($resolver->resolve($rp, $services, $result))
                return true;

        return false;
    }
}
