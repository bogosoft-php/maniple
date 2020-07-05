<?php

declare(strict_types=1);

namespace Tests;

class Car
{
    private Engine $engine;

    function __construct(Engine $engine)
    {
        $this->engine = $engine;
    }

    function getEngine(): Engine
    {
        return $this->engine;
    }
}
