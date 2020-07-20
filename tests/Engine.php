<?php

declare(strict_types=1);

namespace Bogosoft\Maniple\Tests;

class Engine
{
    private int $size;

    function __construct(int $size)
    {
        $this->size = $size;
    }

    function getSize(): int
    {
        return $this->size;
    }
}
