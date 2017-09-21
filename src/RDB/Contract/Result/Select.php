<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract\Result;


interface Select extends Row
{
    public function fetchAll(string $className): array;
    public function fetch(string $className);
}