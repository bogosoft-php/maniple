<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Psr\Container\ContainerInterface as IContainer;
use ReflectionParameter;

/**
 * An implementation of the {@see IParameterResolver} contract that attempts
 * to retrieve the default value of a given parameter.
 *
 * This strategy will be successful when the given parameter has a default
 * value.
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Maniple
 */
final class DefaultValueParameterResolver implements IParameterResolver
{
    /**
     * @inheritDoc
     */
    function resolve(ReflectionParameter $rp, IContainer $services, &$result): bool
    {
        if (!$rp->isDefaultValueAvailable())
            return false;

        /** @noinspection PhpUnhandledExceptionInspection */
        $result = $rp->getDefaultValue();

        return true;
    }
}
