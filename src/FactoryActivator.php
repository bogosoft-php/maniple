<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Bogosoft\Reflection\IParameterResolver;

/**
 * An activator that delegates the responsibility of activating a service
 * to a {@see callable} object.
 *
 * @package Bogosoft\Maniple
 */
class FactoryActivator extends FactoryActivatorBase implements IActivator
{
    /** @var callable */
    private $factory;

    /**
     * Create a new factory activator.
     *
     * @param callable           $factory  An invokable object responsible
     *                                     for activating a service.
     * @param IParameterResolver $resolver A parameter resolver.
     */
    function __construct(callable $factory, IParameterResolver $resolver)
    {
        parent::__construct($resolver);

        $this->factory = $factory;
    }

    /**
     * @inheritDoc
     */
    protected function getFactory(): callable
    {
        return $this->factory;
    }
}
