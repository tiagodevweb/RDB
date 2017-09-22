<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract\Clause;


interface Limitation
{
    public function limit(int $quantity, int $offset = 0);
    public function __toString();
}