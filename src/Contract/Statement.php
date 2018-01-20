<?php

declare(strict_types=1);

namespace Tdw\RDB\Contract;

interface Statement
{
    public function execute();
    public function parameters(): array;
}
