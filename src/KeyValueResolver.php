<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Bogosoft\Reflection\IParameterResolver;
use Bogosoft\Reflection\IPropertyResolver;
use Psr\Container\ContainerInterface as IContainer;
use ReflectionParameter;
use ReflectionProperty;

/**
 * An implementation of the {@see IParameterResolver} and {@see IPropertyResolver}
 * contracts that attempt to resolve a parameter or property using an array of
 * key-value pairs.
 *
 * @package Bogosoft\Maniple
 */
class KeyValueResolver implements IParameterResolver, IPropertyResolver
{
    private array $values;

    /**
     * Create a new key-value resolver.
     *
     * @param array $values
     */
    function __construct(array $values)
    {
        $this->values = $values;
    }

    /**
     * @inheritDoc
     */
    function resolveParameter(ReflectionParameter $rp, IContainer $services, &$result): bool
    {
        if (!array_key_exists($name = $rp->getName(), $this->values))
            return false;

        $result = $this->values[$name];

        return true;
    }

    /**
     * @inheritDoc
     */
    function resolveProperty(ReflectionProperty $rp, IContainer $services, &$result): bool
    {
        if (!array_key_exists($name = $rp->getName(), $this->values))
            return false;

        $result = $this->values[$name];

        return true;
    }
}
