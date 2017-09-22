<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract\Clause;


interface Condition
{
    public function where(string $column, string $operator, string $logicalOperator = 'AND');
    public function between(string $column, string $logicalOperator = 'AND', bool $not = false );
    public function in(string $column, int $countSubSet, string $logicalOperator = 'AND', bool $not = false);
    public function like(string $column, string $logicalOperator = 'AND', bool $not = false);
    public function null(string $column, string $logicalOperator = 'AND', bool $not = false);
    public function __toString();
}