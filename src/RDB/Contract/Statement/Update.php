<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract\Statement;

use Tdw\RDB\Contract\Result\Update as UpdateResult;

interface Update
{
    public function execute(): UpdateResult;
    public function __toString();
}