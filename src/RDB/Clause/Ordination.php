<?php

declare(strict_types=1);

namespace Tdw\RDB\Clause;

use Tdw\RDB\Contract\Clause\Ordination as IOrdination;

class Ordination implements IOrdination
{
    private $columns;
    private $designator;

    public function orderBy(string $columns, $designator = 'ASC')
    {
        $this->columns = $columns;
        $this->designator = $designator;
    }

    public function __toString()
    {
        return ! $this->columns ? '' : sprintf(" ORDER BY %s %s", $this->columns, $this->designator);
    }
}