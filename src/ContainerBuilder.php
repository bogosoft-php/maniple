<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use ErrorException;
use Psr\Container\ContainerInterface as IContainer;
use RuntimeException;

/**
 * A strategy for registering various types of activators to be later used
 * in a service resolution container.
 *
 * @package Bogosoft\Maniple
 */
class ContainerBuilder
{
    private array $activators = [];
    private array $filters    = [];

    /** @var IParameterResolver[] */
    private array $resolvers;

    /**
     * Create a new container builder.
     *
     * @param IParameterResolver[] $resolvers An array of parameter resolvers.
     */
    function __construct(array $resolvers)
    {
        $this->resolvers = $resolvers;
    }

    /**
     * Register an activator.
     *
     * @param  string     $name      A name by which the registered activator
     *                               can be referenced.
     * @param  IActivator $activator An activator to register.
     * @param  bool       $cache     A value indicating whether or not a
     *                               service, once activated, is to be cached.
     * @return $this                 The current container builder.
     */
    function add(string $name, IActivator $activator, bool $cache = false): self
    {
        if ($cache)
            $activator = new CachedActivator($activator);

        $this->activators[$name] = $activator;

        return $this;
    }

    /**
     * Register a class activator.
     *
     * @param  string $name  A name by which the new activator can
     *                       be referenced.
     * @param  string $class The class to be used for activation.
     * @param  bool   $cache A value indicating whether or not a service,
     *                       once activated, is to be cached.
     * @return $this         The current container builder.
     */
    function addClass(string $name, string $class, bool $cache = false): self
    {
        return $this->add($name, new ClassActivator($class, $this->resolvers), $cache);
    }

    /**
     * Register a {@see callable} object as an activator.
     *
     * A factory is expected to be of the form:
     *
     * - fn({@see IContainer}): {@see mixed}
     *
     * @param  string   $name    A name by which the new activator can be
     *                           referenced.
     * @param  callable $factory A factory to be used for activation.
     * @param  bool     $cache   A value indicating whether or not a service,
     *                           once activated, is to be cached.
     * @return $this             The current container builder.
     */
    function addFactory(string $name, callable $factory, bool $cache = false): self
    {
        return $this->add($name, new FactoryActivator($factory, $this->resolvers), $cache);
    }

    /**
     * Register a file-based factory as an activator.
     *
     * A file factory is a file that, when included, returns a closure.
     * The returned closure is expected to be of the form:
     *
     * - fn({@see IContainer}): {@see mixed}
     *
     * @param  string $name  A name by which the new activator can be
     *                       referenced.
     * @param  string $path  The path to a file containing a factory.
     * @param  bool   $cache A value indicating whether or not a service,
     *                       once activated, is to be cached.
     * @return $this         The current container builder.
     */
    function addFileFactory(string $name, string $path, bool $cache = false): self
    {
        return $this->add($name, new FileFactoryActivator($path, $this->resolvers), $cache);
    }

    /**
     * Add a service filter to the current container builder with a given
     * scope.
     *
     * A scope corresponds to the 'id' parameter of the {@see IContainer::get()}
     * method with one exception: a service filter with a scope of '*' will be
     * applied to all service activations.
     *
     * @param  IServiceFilter $filter A service filter.
     * @param  string         $scope  The scope of the given filter.
     * @return $this                  The current container builder.
     */
    function addFilter(IServiceFilter $filter, string $scope = '*'): self
    {
        if (!array_key_exists($scope, $this->filters))
            $this->filters[$scope] = [];

        $this->filters[$scope][] = $filter;

        return $this;
    }

    /**
     * Register an already-instantiated object as a service.
     *
     * @param  mixed       $instance An already instantiated object to be
     *                               directly returned upon 'activation'.
     * @param  string|null $name     A name by which the given instance can
     *                               be referenced. If omitted, the class
     *                               name of the given object will be used
     *                               instead.
     * @return $this                 The current container builder.
     *
     * @throws RuntimeException when the given instance is not an object and
     *                          no name was provided.
     */
    function addInstance($instance, string $name = null): self
    {
        $name ??= @get_class($instance);

        if (null !== ($error = error_get_last()))
        {
            $inner = new ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );

            $message = 'See inner exception.';

            throw new RuntimeException($message, 0, $inner);
        }

        return $this->add($name, new InstanceActivator($instance));
    }

    /**
     * Build a finalized service resolution container.
     */
    function build(): IContainer
    {
        return new ActivatorContainer($this->activators, $this->filters);
    }
}
