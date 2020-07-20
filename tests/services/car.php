<?php

declare(strict_types=1);

use Bogosoft\Maniple\Tests\Car;
use Bogosoft\Maniple\Tests\Engine;

return function(Engine $engine): Car
{
    return new Car($engine);
};
