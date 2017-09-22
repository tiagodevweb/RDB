<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract;

interface Result
{
    public function rowCount(): int;
}