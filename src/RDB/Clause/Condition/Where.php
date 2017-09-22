<?php

declare( strict_types=1 );

namespace Tdw\RDB\Clause\Condition;


class Where
{
    private $column;
    private $operator;
    private $logicalOperator;

    public function __construct(string $column, string $operator, string $logicalOperator = 'AND')
    {
        $this->column = $column;
        $this->operator = $operator;
        $this->logicalOperator = $logicalOperator;
    }

    public function __toString()
    {
        return sprintf( " %s %s %s ?", strtoupper( $this->logicalOperator ), $this->column, $this->operator );
    }
}