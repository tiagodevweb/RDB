<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract\Result;


interface Insert extends Row
{
    public function lastInsertId(): int;
}