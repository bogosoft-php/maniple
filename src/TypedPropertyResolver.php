<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Psr\Container\ContainerInterface as IContainer;
use ReflectionNamedType;
use ReflectionProperty;

/**
 * A strategy for resolving a class property by reflecting upon its type
 * hint.
 *
 * @package Bogosoft\Maniple
 */
class TypedPropertyResolver implements IPropertyResolver
{
    /**
     * @inheritDoc
     */
    function resolve(ReflectionProperty $rp, IContainer $services, &$result): bool
    {
        if (
            null !== ($type = $rp->getType())
            && $type instanceof ReflectionNamedType
            && $services->has($name = $type->getName())
            )
        {
            $result = $services->get($name);

            return true;
        }

        return false;
    }
}
