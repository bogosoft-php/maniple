<?php

namespace Bogosoft\Maniple;

use Psr\Container\ContainerInterface as IContainer;

/**
 * A strategy for influencing the outcome of an activation.
 *
 * @package Bogosoft\Maniple
 */
interface IServiceFilter
{
    /**
     * Apply the current filter to a service activation.
     *
     * @param  IContainer $container A service resolver.
     * @param  IActivator $activator A service activator.
     * @return mixed                 An activated service.
     */
    function filter(IContainer $container, IActivator $activator);
}
