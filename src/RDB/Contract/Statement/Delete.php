<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract\Statement;

use Tdw\RDB\Contract\Result\Delete as DeleteResult;

interface Delete
{
    public function execute(): DeleteResult;
    public function __toString();
}