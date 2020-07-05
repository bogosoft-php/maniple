<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Psr\Container\NotFoundExceptionInterface as INotFoundException;
use RuntimeException;
use Throwable;

/**
 * An exception intended to be thrown when an unknown service is requested
 * from a service resolver.
 *
 * @package Bogosoft\Maniple
 */
class NotFoundException extends RuntimeException implements INotFoundException
{
    /**
     * Create a new service-not-found exception.
     *
     * @param string         $service  The name of the service that could
     *                                 not be found.
     * @param Throwable|null $previous An optional, throwable object that
     *                                 preceded the new not found
     *                                 exception.
     */
    function __construct(string $service, Throwable $previous = null)
    {
        $message = "Service not found: '$service'.";

        parent::__construct($message, 0, $previous);
    }
}
