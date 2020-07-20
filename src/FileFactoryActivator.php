<?php /** @noinspection PhpIncludeInspection */

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Closure;
use RuntimeException;
use Psr\Container\ContainerInterface as IContainer;

/**
 * An implementation of the {@see IActivator} contract that delegates the
 * activation of a service to a {@see Closure} returned from a file.
 *
 * The closure is expected to be of the form:
 *
 * - fn({@see IContainer}): {@see mixed}
 *
 * @package Bogosoft\Maniple
 */
class FileFactoryActivator extends FactoryActivatorBase implements IActivator
{
    private string $path;

    /**
     * Create a new file factory activator.
     *
     * @param string               $path   The path to a file that returns
     *                                     a factory.
     * @param IParameterResolver $resolver A parameter resolver.
     */
    function __construct(string $path, IParameterResolver $resolver)
    {
        parent::__construct($resolver);

        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    protected function getFactory(): callable
    {
        if (!is_file($this->path))
            throw new RuntimeException("File not found: '{$this->path}'.");

        return include $this->path;
    }
}
