<?php

declare( strict_types=1 );

namespace Tdw\RDB\Clause\Condition;


class In
{
    private $column;
    private $countSubSet;
    private $logicalOperator;
    private $not;

    public function __construct(string $column, int $countSubSet, string $logicalOperator = 'AND', bool $not = false)
    {
        $this->column = $column;
        $this->countSubSet = $countSubSet;
        $this->logicalOperator = $logicalOperator;
        $this->not = $not;
    }

    public function __toString()
    {
        $subSet = '';
        for ( $i = 0; $i < $this->countSubSet; $i++ ) {
            $subSet .= '?, ';
        }
        $not = $this->not ? 'NOT ' : '';
        return sprintf( " %s %s %sIN ( %s )", strtoupper( $this->logicalOperator ), $this->column, $not, rtrim( $subSet, ', ' ) );
    }
}