<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Bogosoft\Reflection\IParameterResolver;
use Psr\Container\ContainerInterface as IContainer;
use Throwable;

/**
 * A service resolution strategy that relies on activators keyed by name.
 *
 * Filters can be affixed to objects of this class to influence the outcome
 * of resolving a service.
 *
 * This class cannot be inherited.
 *
 * @package Bogosoft\Maniple
 */
final class ActivatorContainer implements IContainer
{
    private array $activators;
    private array $filters;
    private IParameterResolver $resolver;

    /**
     * Create a new activator container.
     *
     * @param array              $activators A collection of key-value pairs
     *                                       where the key is a service name,
     *                                       and the value is an
     *                                       {@see IActivator} object.
     * @param array              $filters    A collection of key-value pairs
     *                                       where the key is a service name
     *                                       (or scope), and the value is an
     *                                       {@see IServiceFilter} object.
     * @param IParameterResolver $resolver   A parameter resolver.
     */
    function __construct(
        array $activators,
        array $filters,
        IParameterResolver $resolver
        )
    {
        $this->activators = $activators;
        $this->filters    = $filters;
        $this->resolver   = $resolver;
    }

    /**
     * @inheritDoc
     */
    function get($id)
    {
        /** @var IActivator $activator */
        $activator = null;

        if (array_key_exists($id, $this->activators))
            $activator = $this->activators[$id];
        elseif (class_exists($id))
            $activator = new ClassActivator($id, $this->resolver);

        if (null === $activator)
            throw new NotFoundException($id);

        if (count($filters = [...$this->getFilters($id)]) > 0)
            $activator = new FilteredActivator($activator, ...$filters);

        try
        {
            return $activator->activate($this);
        }
        catch (Throwable $t)
        {
            $message = 'See inner exception.';

            throw new ContainerException($message, 0, $t);
        }
    }

    private function getFilters(string $id): iterable
    {
        yield from $this->filters['*'] ?? [];
        yield from $this->filters[$id] ?? [];
    }

    /**
     * @inheritDoc
     */
    function has($id)
    {
        return array_key_exists($id, $this->activators)
            || class_exists($id);
    }
}
