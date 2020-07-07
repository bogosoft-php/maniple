<?php

namespace Bogosoft\Maniple;

use Psr\Container\ContainerInterface as IContainer;
use ReflectionProperty;

/**
 * Represents a strategy for resolving properties of a class.
 *
 * @package Bogosoft\Maniple
 */
interface IPropertyResolver
{
    /**
     * Attempt to resolve a given property.
     *
     * @param  ReflectionProperty $rp       A class property.
     * @param  IContainer         $services A service resolution container.
     * @param  mixed              $result   The result of resolving the value
     *                                      of a given property.
     * @return bool                         A value indicating whether or not
     *                                      the given property was successfully
     *                                      resolved.
     */
    function resolve(ReflectionProperty $rp, IContainer $services, &$result): bool;
}
