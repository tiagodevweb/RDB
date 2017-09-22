<?php

declare(strict_types=1);

namespace Tdw\RDB\Clause;

use Tdw\RDB\Contract\Clause\Limitation as ILimitation;

class Limitation implements ILimitation
{
    private $limit;

    public function limit(int $quantity, int $offset = 0)
    {
        if ( $quantity <= 0 ) {
            return;
        }
        if ( $offset > 0 ) {
            $this->limit = intval($quantity).' OFFSET '.intval($offset);
            return;
        }
        $this->limit = intval($quantity);
    }

    public function __toString()
    {
        return ! $this->limit ? '' : " LIMIT {$this->limit}";
    }
}