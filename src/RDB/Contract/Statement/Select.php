<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract\Statement;

use Tdw\RDB\Contract\Result\Select as SelectResult;

interface Select extends Relation, Condition
{
    public function join(
        string $childTable, string $foreignKeyChild, string $operator, string $primaryKeyParent
    );
    public function leftJoin(
        string $childTable, string $foreignKeyChild, string $operator, string $primaryKeyParent
    );
    public function rightJoin(
        string $childTable, string $foreignKeyChild, string $operator, string $primaryKeyParent
    );
    public function fullJoin(
        string $childTable, string $foreignKeyChild, string $operator, string $primaryKeyParent
    );
    public function where(string $column, string $operator, $value);
    public function orWhere(string $column, string $operator, $value);
    public function between(string $column, $valueOne, $valueTwo);
    public function notBetween(string $column, $valueOne, $valueTwo);
    public function orBetween(string $column, $valueOne, $valueTwo);
    public function orNotBetween(string $column, $valueOne, $valueTwo);
    public function in(string $column, array $subSet);
    public function notIn(string $column, array $subSet);
    public function orIn(string $column, array $subSet);
    public function orNotIn(string $column, array $subSet);
    public function exists(string $column, string $subSQL);
    public function notExists(string $column, string $subSQL);
    public function orExists(string $column, string $subSQL);
    public function orNotExists(string $column, string $subSQL);
    public function like(string $column, string $value);
    public function orLike(string $column, string $value);
    public function notLike(string $column, string $value);
    public function orNotLike(string $column, string $value);
    public function null(string $column);
    public function orNull(string $column);
    public function notNull(string $column);
    public function orNotNull(string $column);
    public function groupBy(string $columns);
    public function orderBy(string $columns, $designator = 'ASC');
    public function limit(int $quantity, int $offset = 0);
    public function execute(): SelectResult;
    public function __toString();
}