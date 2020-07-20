<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Bogosoft\Reflection\IParameterResolver;
use Psr\Container\ContainerInterface as IContainer;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use RuntimeException;

/**
 * An activator that creates an object of a given class via reflection.
 *
 * @package Bogosoft\Maniple
 */
final class ClassActivator implements IActivator
{
    private string $class;
    private IParameterResolver $resolver;

    /**
     * Create a new class activator.
     *
     * @param string             $class    The name of a class from which
     *                                     a service will be created.
     * @param IParameterResolver $resolver A parameter resolver.
     */
    function __construct(string $class, IParameterResolver $resolver)
    {
        $this->class    = $class;
        $this->resolver = $resolver;
    }

    /**
     * @inheritDoc
     *
     * @throws ReflectionException
     */
    function activate(IContainer $services)
    {
        $rc = new ReflectionClass($this->class);

        if (null === ($ctor = $rc->getConstructor()))
            return $rc->newInstance();

        $resolve = function(ReflectionParameter $rp) use (&$rc, &$services)
        {
            if ($this->resolver->resolve($rp, $services, $result))
                return $result;

            $parameter = "{$rc->name}::__construct::\${$rp->name}";

            $message = "Unresolvable parameter: '$parameter'.";

            throw new RuntimeException($message);
        };

        $args = array_map($resolve, $ctor->getParameters());

        return $rc->newInstanceArgs($args);
    }
}
