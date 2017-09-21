<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract\Result;


interface Row
{
    public function rowCount(): int;
}