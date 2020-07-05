<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Psr\Container\ContainerInterface as IContainer;

/**
 * An implementation of the {@see IActivator} contract that will cache a
 * service activated by another activator.
 *
 * @package Bogosoft\Maniple
 */
final class CachedActivator implements IActivator
{
    private IActivator $source;

    /** @var mixed|null */
    private $instance = null;

    /**
     * Create a new cached activator.
     *
     * @param IActivator $source A source activator from which a service to
     *                           be cached will be activated.
     */
    function __construct(IActivator $source)
    {
        $this->source = $source;
    }

    /**
     * @inheritDoc
     */
    function activate(IContainer $services)
    {
        return $this->instance ?? ($this->instance = $this->source->activate($services));
    }
}
