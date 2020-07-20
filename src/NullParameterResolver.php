<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Psr\Container\ContainerInterface as IContainer;
use ReflectionParameter;

/**
 * An implementation of the {@see IParameterResolver} contract that will not
 * resolve a reflection parameter under any circumstances.
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Maniple
 */
final class NullParameterResolver implements IParameterResolver
{
    /**
     * @inheritDoc
     */
    function resolve(ReflectionParameter $rp, IContainer $services, &$result): bool
    {
        return false;
    }
}
