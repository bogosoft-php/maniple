<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Psr\Container\ContainerInterface as IContainer;

/**
 * A service activator that merely returns a pre-instantiated object when
 * 'activated'.
 *
 * @package Bogosoft\Maniple
 */
final class InstanceActivator implements IActivator
{
    /** @var mixed */
    private $instance;

    /**
     * Create a new instance activator.
     *
     * @param mixed $instance An instantiated object.
     */
    function __construct($instance)
    {
        $this->instance = $instance;
    }

    /**
     * @inheritDoc
     */
    function activate(IContainer $services, $service = null)
    {
        return $this->instance;
    }
}
