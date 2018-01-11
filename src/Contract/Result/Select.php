<?php

declare(strict_types=1);

namespace Tdw\RDB\Contract\Result;

use Tdw\RDB\Contract\Collection;
use Tdw\RDB\Contract\Result;

interface Select extends Result
{
    public function fetchAll(): Collection;
    public function fetch(): array;
}
