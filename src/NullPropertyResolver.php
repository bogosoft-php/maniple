<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Psr\Container\ContainerInterface as IContainer;
use ReflectionProperty;

/**
 * An implementation of the {@see IPropertyResolver} contract that will not
 * resolve a reflection property under any circumstance.
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Maniple
 */
final class NullPropertyResolver implements IPropertyResolver
{
    /**
     * @inheritDoc
     */
    function resolve(ReflectionProperty $rp, IContainer $services, &$result): bool
    {
        return false;
    }
}
