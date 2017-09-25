<?php

declare(strict_types=1);

namespace Tdw\RDB\Statement;

use Tdw\RDB\Clause\{Condition,Relation,Grouping,Limitation,Ordination};
use Tdw\RDB\Contract\Statement\Select as SelectStatement;
use Tdw\RDB\Contract\Result\Select as ISelectResult;
use Tdw\RDB\Exception\StatementExecuteException;
use Tdw\RDB\Result\Select as SelectResult;

class Select implements SelectStatement
{
    /**
     * @var \PDO
     */
    private $pdo;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string[]
     */
    private $columns;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var Relation
     */
    private $relation;

    /**
     * @var Condition
     */
    private $condition;

    /**
     * @var Ordination
     */
    private $ordination;

    /**
     * @var Limitation
     */
    private $limitation;

    public function __construct(\PDO $pdo, string $table, array $columns = ['*'])
    {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->columns = $columns;
        $this->relation = new Relation();
        $this->condition = new Condition();
        $this->ordination = new Ordination();
        $this->limitation = new Limitation();
    }

    public function columns(array $columns): SelectStatement
    {
        $this->columns = $columns;
        return $this;
    }

    public function join(
        string $childTable, string $foreignKeyChild, string $operator, string $primaryKeyParent
    ): SelectStatement
    {
        $this->relation->join($childTable, $foreignKeyChild, $operator, $primaryKeyParent);
        return $this;
    }

    public function leftJoin(
        string $childTable, string $foreignKeyChild, string $operator, string $primaryKeyParent
    ): SelectStatement
    {
        $this->relation->join($childTable, $foreignKeyChild, $operator, $primaryKeyParent, 'LEFT OUTER');
        return $this;
    }

    public function rightJoin(
        string $childTable, string $foreignKeyChild, string $operator, string $primaryKeyParent
    ): SelectStatement
    {
        $this->relation->join($childTable, $foreignKeyChild, $operator, $primaryKeyParent, 'RIGHT OUTER');
        return $this;
    }

    public function fullJoin(
        string $childTable, string $foreignKeyChild, string $operator, string $primaryKeyParent
    ): SelectStatement
    {
        $this->relation->join($childTable, $foreignKeyChild, $operator, $primaryKeyParent, 'FULL OUTER');
        return $this;
    }

    public function where(string $column, string $operator, $value): SelectStatement
    {
        $this->condition->where($column, $operator);
        $this->parameters[] = $value;
        return $this;
    }

    public function orWhere(string $column, string $operator, $value): SelectStatement
    {
        $this->condition->where($column, $operator, 'OR');
        $this->parameters[] = $value;
        return $this;
    }

    public function between(string $column, $valueOne, $valueTwo): SelectStatement
    {
        $this->condition->between($column);
        $this->parameters[] = $valueOne;
        $this->parameters[] = $valueTwo;
        return $this;
    }

    public function notBetween(string $column, $valueOne, $valueTwo): SelectStatement
    {
        $this->condition->between($column, 'AND', $not = true);
        $this->parameters[] = $valueOne;
        $this->parameters[] = $valueTwo;
        return $this;
    }

    public function orBetween(string $column, $valueOne, $valueTwo): SelectStatement
    {
        $this->condition->between($column, 'OR');
        $this->parameters[] = $valueOne;
        $this->parameters[] = $valueTwo;
        return $this;
    }

    public function orNotBetween(string $column, $valueOne, $valueTwo): SelectStatement
    {
        $this->condition->between($column, 'OR', $not = true);
        $this->parameters[] = $valueOne;
        $this->parameters[] = $valueTwo;
        return $this;
    }

    public function in(string $column, array $subSet): SelectStatement
    {
        $this->condition->in($column,count($subSet));
        foreach ($subSet as $item) {
            $this->parameters[] = $item;
        }
        return $this;
    }

    public function notIn(string $column, array $subSet): SelectStatement
    {
        $this->condition->in($column,count($subSet), 'AND', $not = true);
        foreach ($subSet as $item) {
            $this->parameters[] = $item;
        }
        return $this;
    }

    public function orIn(string $column, array $subSet): SelectStatement
    {
        $this->condition->in($column,count($subSet), 'OR');
        foreach ($subSet as $item) {
            $this->parameters[] = $item;
        }
        return $this;
    }

    public function orNotIn(string $column, array $subSet): SelectStatement
    {
        $this->condition->in($column,count($subSet), 'OR', $not = true);
        foreach ($subSet as $item) {
            $this->parameters[] = $item;
        }
        return $this;
    }

    public function like(string $column, string $value): SelectStatement
    {
        $this->condition->like($column);
        $this->parameters[] = $value;
        return $this;
    }

    public function orLike(string $column, string $value): SelectStatement
    {
        $this->condition->like($column, 'OR');
        $this->parameters[] = $value;
        return $this;
    }

    public function notLike(string $column, string $value): SelectStatement
    {
        $this->condition->like($column, 'AND', $not = true);
        $this->parameters[] = $value;
        return $this;
    }

    public function orNotLike(string $column, string $value): SelectStatement
    {
        $this->condition->like($column, 'OR', $not = true);
        $this->parameters[] = $value;
        return $this;
    }

    public function null(string $column): SelectStatement
    {
        $this->condition->null($column);
        return $this;
    }

    public function orNull(string $column): SelectStatement
    {
        $this->condition->null($column, 'OR');
        return $this;
    }

    public function notNull(string $column): SelectStatement
    {
        $this->condition->null($column, 'AND', $not = true);
        return $this;
    }

    public function orNotNull(string $column): SelectStatement
    {
        $this->condition->null($column, 'OR', $not = true);
        return $this;
    }

    public function orderBy(string $columns, $designator = 'ASC'): SelectStatement
    {
        $this->ordination->orderBy($columns,$designator);
        return $this;
    }

    public function limit(int $quantity, int $offset = 0): SelectStatement
    {
        $this->pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES,false);
        $this->limitation->limit($quantity,$offset);
        $this->parameters[] = $quantity;
        if ($offset > 0){
            $this->parameters[] = $offset;
        }
        return $this;
    }

    public function execute(): ISelectResult
    {
        try {
            if (empty($this->parameters())) {
                $stmt = $this->pdo->query((string)$this);
            } else {
                $stmt = $this->pdo->prepare((string)$this);
                $stmt->execute($this->parameters());
            }
            return new SelectResult($stmt);
        } catch (\PDOException $e) {
            throw new StatementExecuteException("Execution of select statement failed", 0, $e);
        }
    }

    public function __toString(): string
    {
        return $this->_sql();
    }

    public function parameters(): array
    {
        return array_values($this->parameters);
    }

    private function _sql()
    {
        $sql = 'SELECT ';
        $sql .= implode(', ', $this->columns);
        $sql .= " FROM {$this->table}";
        $sql .= (string)$this->relation;
        $sql .= (string)$this->condition;
        $sql .= (string)$this->ordination;
        $sql .= (string)$this->limitation;
        return $sql;
    }
}