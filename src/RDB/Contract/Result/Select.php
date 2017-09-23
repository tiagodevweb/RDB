<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract\Result;


use Tdw\RDB\Contract\Result;

interface Select extends Result
{
    public function fetchAll(int $style = \PDO::FETCH_ASSOC): array;
    public function fetch(string $className);
}