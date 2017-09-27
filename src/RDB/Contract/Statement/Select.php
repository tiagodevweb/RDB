<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract\Statement;

use Tdw\RDB\Contract\Statement;

interface Select extends Statement
{
    public function columns(array $columns): Select;
    public function join(
        string $childTable,
        string $foreignKeyChild,
        string $operator,
        string $primaryKeyParent
    ): Select;
    public function where(string $column, string $operator, $value): Select;
    public function orWhere(string $column, string $operator, $value): Select;
    public function between(string $column, $valueOne, $valueTwo): Select;
    public function notBetween(string $column, $valueOne, $valueTwo): Select;
    public function orBetween(string $column, $valueOne, $valueTwo): Select;
    public function orNotBetween(string $column, $valueOne, $valueTwo): Select;
    public function in(string $column, array $subSet): Select;
    public function notIn(string $column, array $subSet): Select;
    public function orIn(string $column, array $subSet): Select;
    public function orNotIn(string $column, array $subSet): Select;
    public function like(string $column, string $value): Select;
    public function orLike(string $column, string $value): Select;
    public function notLike(string $column, string $value): Select;
    public function orNotLike(string $column, string $value): Select;
    public function null(string $column): Select;
    public function orNull(string $column): Select;
    public function notNull(string $column): Select;
    public function orNotNull(string $column): Select;
    public function orderBy(string $columns, $designator = 'ASC'): Select;
    public function limit(int $count, int $offset = 0): Select;
}
