<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract;

interface Statement
{
    public function execute();
    public function __toString(): string;
    public function parameters(): array;
}