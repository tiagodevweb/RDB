<?php

declare(strict_types=1);

namespace Tdw\RDB\Clause;

use Tdw\RDB\Contract\Clause\Limitation as ILimitation;

class Limitation implements ILimitation
{
    private $syntax;

    public function limit(int $quantity, int $offset = 0)
    {
        if ( $quantity <= 0 ) {
            return;
        }
        if ( $offset > 0 ) {
            $this->syntax = '? OFFSET ?';
            return;
        }
        $this->syntax = '?';
    }

    public function __toString()
    {
        return ! $this->syntax ? '' : " LIMIT {$this->syntax}";
    }
}