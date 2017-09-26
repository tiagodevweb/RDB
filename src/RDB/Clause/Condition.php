<?php

declare(strict_types=1);

namespace Tdw\RDB\Clause;

use Tdw\RDB\Clause\Condition\Between;
use Tdw\RDB\Clause\Condition\In;
use Tdw\RDB\Clause\Condition\IsNull;
use Tdw\RDB\Clause\Condition\Like;
use Tdw\RDB\Clause\Condition\Where;
use Tdw\RDB\Contract\Clause\Condition as ICondition;

class Condition implements ICondition
{
    private $conditions = [];

    public function where(string $column, string $operator, string $logicalOperator = 'AND')
    {
        $this->conditions[] = (string) new Where($column, $operator, $logicalOperator);
    }

    public function between(string $column, string $logicalOperator = 'AND', bool $not = false)
    {
        $this->conditions[] = (string) new Between($column, $logicalOperator, $not);
    }

    public function in(string $column, int $countSubSet, string $logicalOperator = 'AND', bool $not = false)
    {
        $this->conditions[] = (string) new In($column, $countSubSet, $logicalOperator, $not);
    }

    public function like(string $column, string $logicalOperator = 'AND', bool $not = false)
    {
        $this->conditions[] = (string) new Like($column, $logicalOperator, $not);
    }

    public function null(string $column, string $logicalOperator = 'AND', bool $not = false)
    {
        $this->conditions[] = (string) new IsNull($column, $logicalOperator, $not);
    }

    public function __toString()
    {
        return empty($this->conditions) ? '' : $this->concatWhere();
    }

    private function concatWhere()
    {
        return ' WHERE ' . ltrim(implode('', $this->conditions), ' AND');
    }
}
