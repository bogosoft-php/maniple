<?php

declare(strict_types=1);

namespace Bogosoft\Maniple\Tests;

use Bogosoft\Maniple\NotFoundException;
use Psr\Container\ContainerInterface as IContainer;

final class EmptyContainer implements IContainer
{
    /**
     * @inheritDoc
     */
    public function get($id)
    {
        throw new NotFoundException($id);
    }

    /**
     * @inheritDoc
     */
    public function has($id)
    {
        return false;
    }
}
