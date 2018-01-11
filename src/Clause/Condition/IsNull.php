<?php

declare(strict_types=1);

namespace Tdw\RDB\Clause\Condition;

class IsNull
{
    private $column;
    private $logicalOperator;
    private $not;

    public function __construct(string $column, string $logicalOperator = 'AND', bool $not = false)
    {
        $this->column = $column;
        $this->logicalOperator = $logicalOperator;
        $this->not = $not;
    }

    public function __toString()
    {
        $not = $this->not ? 'NOT ' : '';
        return sprintf(" %s %s IS %sNULL", $this->logicalOperator, $this->column, $not);
    }
}
