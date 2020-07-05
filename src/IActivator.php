<?php

namespace Bogosoft\Maniple;

use Psr\Container\ContainerInterface as IContainer;

/**
 * Represents a strategy for activating a service.
 *
 * @package Bogosoft\Maniple
 */
interface IActivator
{
    /**
     * Activate the service associated with the current activator.
     *
     * @param  IContainer $services A service resolver.
     * @return mixed                An activated service.
     */
    function activate(IContainer $services);
}
