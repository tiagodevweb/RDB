<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract\Statement;

use Tdw\RDB\Contract\Result\Insert as InsertResult;

interface Insert
{
    public function execute(): InsertResult;
    public function __toString();
}