<?php

declare(strict_types=1);

namespace Bogosoft\Maniple;

use Psr\Container\ContainerExceptionInterface as IContainerException;
use RuntimeException;

/**
 * A general exception intended to be thrown within the context of working
 * with containers.
 *
 * @package Bogosoft\Maniple
 */
class ContainerException extends RuntimeException implements IContainerException
{
}
