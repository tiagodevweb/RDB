<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract\Result;


use Tdw\RDB\Contract\Result;

interface Insert extends Result
{
    public function lastInsertId(string $name = null): int;
}