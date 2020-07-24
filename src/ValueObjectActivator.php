<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Bogosoft\Reflection\IPropertyResolver;
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
    static function __set_state($data)
    {
        return new ValueObjectActivator($data['class'], $data['resolver']);
    }

    private string $class;
    private IPropertyResolver $resolver;

    /**
     * Create a new value object activator.
     *
     * @param string              $class  The name of the class of a value
     *                                    object.
     * @param IPropertyResolver $resolver A property resolver.
     */
    function __construct(string $class, IPropertyResolver $resolver)
    {
        $this->class    = $class;
        $this->resolver = $resolver;
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
            if ($this->resolver->resolveProperty($rp, $services, $result))
                $rp->setValue($service, $result);

        return $service;
    }
}
