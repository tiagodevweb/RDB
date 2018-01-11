<?php

declare(strict_types=1);

namespace Tdw\RDB\Contract\Clause;

interface Ordination
{
    public function orderBy(string $columns, $designator);
    public function __toString();
}
