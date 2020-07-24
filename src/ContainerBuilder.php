<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Bogosoft\Reflection\CompositeParameterResolver;
use Bogosoft\Reflection\CompositePropertyResolver;
use Bogosoft\Reflection\IParameterResolver;
use Bogosoft\Reflection\IPropertyResolver;
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
    private array $filters = [];
    private IParameterResolver $parameterResolver;
    private IPropertyResolver $propertyResolver;

    /**
     * Create a new container builder.
     *
     * @param IParameterResolver $parameterResolver A parameter resolver.
     * @param IPropertyResolver  $propertyResolver  A property resolver.
     */
    function __construct(
        IParameterResolver $parameterResolver,
        IPropertyResolver $propertyResolver
        )
    {
        $this->parameterResolver = $parameterResolver;
        $this->propertyResolver  = $propertyResolver;
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
     * @param  string $name   A name by which the new activator can be
     *                        referenced.
     * @param  string $class  The class to be used for activation.
     * @param  bool   $cache  A value indicating whether or not a service,
     *                        once activated, is to be cached.
     * @param  array  $params An optional collection of key-value pairs.
     * @return $this          The current container builder.
     */
    function addClass(
        string $name,
        string $class,
        bool $cache = false,
        array $params = []
        )
        : self
    {
        $resolver = $this->parameterResolver;

        if (count($params) > 0)
            $resolver = new CompositeParameterResolver(
                $resolver,
                new KeyValueResolver($params)
                );

        return $this->add($name, new ClassActivator($class, $resolver), $cache);
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
     * @param  array    $params  An optional collection of key-value pairs.
     * @return $this             The current container builder.
     */
    function addFactory(
        string $name,
        callable $factory,
        bool $cache = false,
        array $params = []
        )
        : self
    {
        $resolver = $this->parameterResolver;

        if (count($params) > 0)
            $resolver = new CompositeParameterResolver(
                $resolver,
                new KeyValueResolver($params)
            );

        return $this->add($name, new FactoryActivator($factory, $resolver), $cache);
    }

    /**
     * Register a file-based factory as an activator.
     *
     * A file factory is a file that, when included, returns a closure.
     * The returned closure is expected to be of the form:
     *
     * - fn({@see IContainer}): {@see mixed}
     *
     * @param  string $name   A name by which the new activator can be
     *                        referenced.
     * @param  string $path   The path to a file containing a factory.
     * @param  bool   $cache  A value indicating whether or not a service,
     *                        once activated, is to be cached.
     * @param  array  $params An optional collection of key-value pairs.
     * @return $this          The current container builder.
     */
    function addFileFactory(
        string $name,
        string $path,
        bool $cache = false,
        array $params = []
        )
        : self
    {
        $resolver = $this->parameterResolver;

        if (count($params) > 0)
            $resolver = new CompositeParameterResolver(
                $resolver,
                new KeyValueResolver($params)
                );

        return $this->add($name, new FileFactoryActivator($path, $resolver), $cache);
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
     * Register a class representing a value object as an activator.
     *
     * A value class is traditionally considered to be an object public
     * properties and no methods, including a constructor.
     *
     * @param  string      $class  The class of a value object.
     * @param  string|null $name   An optional name under which the new value
     *                             object activator will be registered.
     * @param  bool        $cache  A value indicating whether or not a service,
     *                             once activated, is to be cached.
     * @param  array       $params An optional collection of key-value pairs.
     * @return $this               The current container builder.
     */
    function addValueClass(
        string $class,
        string $name = null,
        bool $cache = false,
        array $params = []
        )
        : self
    {
        $resolver = $this->propertyResolver;

        if (count($params) > 0)
            $resolver = new CompositePropertyResolver(
                $resolver,
                new KeyValueResolver($params)
                );

        $activator = new ValueObjectActivator($class, $resolver);

        return $this->add($name ?? $class, $activator, $cache);
    }

    /**
     * Build a finalized service resolution container.
     */
    function build(): IContainer
    {
        return new ActivatorContainer(
            $this->activators,
            $this->filters,
            $this->parameterResolver
            );
    }
}
