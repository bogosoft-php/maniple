<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Psr\Container\ContainerInterface as IContainer;
use ReflectionParameter;

/**
 * An implementation of the {@see IParameterResolver} contract that attempts
 * to assign a given container to a given parameter.
 *
 * This strategy will be successful when a given parameter is type-hinted with
 * the {@see IContainer} interface.
 *
 * The class cannot be inherited.
 *
 * @package Bogosoft\Maniple
 */
final class ContainerParameterResolver implements IParameterResolver
{
    /**
     * @inheritDoc
     */
    function resolve(ReflectionParameter $rp, IContainer $services, &$result): bool
    {
        if (
            null === ($type = $rp->getType())
            || !($type instanceof \ReflectionNamedType)
            || $type->getName() !== IContainer::class
            )
            return false;

        $result = $services;

        return true;
    }
}
