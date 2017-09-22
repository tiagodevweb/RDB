<?php

declare(strict_types=1);

namespace Tdw\RDB\Clause;

use Tdw\RDB\Contract\Clause\Grouping as IGrouping;

class Grouping implements IGrouping
{
    private $columns;

    public function groupBy(string $columns)
    {
        $this->columns = $columns;
    }

    public function __toString()
    {
        return ! $this->columns ? '' : sprintf(" GROUP BY %s", $this->columns);
    }
}