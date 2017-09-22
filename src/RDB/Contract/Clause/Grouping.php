<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract\Clause;


interface Grouping
{
    public function groupBy(string $columns);
    public function __toString();
}