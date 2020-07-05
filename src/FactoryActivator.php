<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

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
     * @param callable             $factory   An invokable object responsible
     *                                        for activating a service.
     * @param IParameterResolver[] $resolvers An array of parameter resolvers.
     */
    function __construct(callable $factory, array $resolvers)
    {
        parent::__construct($resolvers);

        $this->factory   = $factory;
    }

    /**
     * @inheritDoc
     */
    protected function getFactory(): callable
    {
        return $this->factory;
    }
}
