<?php

declare(strict_types=1);

use Tests\Car;
use Tests\Engine;

return function(Engine $engine): Car
{
    return new Car($engine);
};
