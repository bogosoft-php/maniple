<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Psr\Container\ContainerInterface as IContainer;

/**
 * A filtered implementation of the {@see IActivator} contract.
 *
 * @package Bogosoft\Maniple
 */
final class FilteredActivator implements IActivator
{
    static function __set_state($data)
    {
        return new FilteredActivator($data['activator'], $data['filters']);
    }

    private IActivator $activator;

    /** @var IServiceFilter[] */
    public array $filters;

    /**
     * Create a new filtered activator.
     *
     * @param IActivator          $activator A service activator.
     * @param IServiceFilter   ...$filters   Zero or more filters to be
     *                                       applied to the given activator
     *                                       when activating a service.
     */
    function __construct(IActivator $activator, IServiceFilter ...$filters)
    {
        $this->activator = $activator;
        $this->filters   = $filters;
    }

    /**
     * @inheritDoc
     */
    function activate(IContainer $services)
    {
        return (new class($this->activator, $this->filters) implements IActivator
        {
            private IActivator $activator;
            private array $filters;

            function __construct(IActivator $activator, array $filters)
            {
                $this->activator = $activator;
                $this->filters   = $filters;
            }

            function activate(IContainer $services)
            {
                /** @var IServiceFilter $filter */
                $filter = array_shift($this->filters);

                return null === $filter
                    ? $this->activator->activate($services)
                    : $filter->filter($services, $this);
            }

        })->activate($services);
    }
}
