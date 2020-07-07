<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Psr\Container\ContainerInterface as IContainer;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;

/**
 * An activator which specializes in activating value objects as services.
 *
 * @package Bogosoft\Maniple
 */
class ValueObjectActivator implements IActivator
{
    private string $class;

    /** @var IPropertyResolver[] */
    private array $resolvers;

    /**
     * Create a new value object activator.
     *
     * @param string              $class     The name of the class of a value
     *                                       object.
     * @param IPropertyResolver[] $resolvers An array of property resolvers.
     */
    function __construct(string $class, array $resolvers)
    {
        $this->class     = $class;
        $this->resolvers = $resolvers;
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException when the requested class cannot be
     *                             reflected upon.
     */
    function activate(IContainer $services)
    {
        $rc = new ReflectionClass($this->class);

        $service = $rc->newInstanceArgs();

        $filter = ReflectionProperty::IS_PUBLIC;

        foreach ($rc->getProperties($filter) as $rp)
            foreach ($this->resolvers as $resolver)
                if ($resolver->resolve($rp, $services, $result))
                {
                    $rp->setValue($service, $result);

                    break;
                }

        return $service;
    }
}
