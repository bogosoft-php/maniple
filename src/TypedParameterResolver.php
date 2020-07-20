<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Psr\Container\ContainerInterface as IContainer;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * An implementation of the {@see IParameterResolver} contract that attempts
 * to resolve a service by keying off of the name of the type of a given
 * parameter.
 *
 * This resolver is only successful when ...
 *
 * - The given parameter has been type-hinted.
 * - A corresponding service can be resolved from the given container by the
 *   name of the type of the given parameter.
 *
 * @package Bogosoft\Maniple
 */
class TypedParameterResolver implements IParameterResolver
{
    /**
     * @inheritDoc
     */
    function resolve(ReflectionParameter $rp, IContainer $services, &$result): bool
    {
        if (
            null === ($type = $rp->getType())
            || !($type instanceof ReflectionNamedType)
            || !$services->has($name = $type->getName())
            )
            return false;

        $result = $services->get($name);

        return true;
    }
}
