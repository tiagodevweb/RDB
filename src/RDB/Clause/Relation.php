<?php

declare(strict_types=1);

namespace Tdw\RDB\Clause;

use Tdw\RDB\Contract\Clause\Relation as IRelation;

class Relation implements IRelation
{
    private $join;

    public function join(
        string $childTable, string $foreignKeyChild, string $operator,
        string $primaryKeyParent, string $type = 'INNER'
    )
    {
        $this->join = sprintf(" %s JOIN %s ON (%s %s %s)", $type, $childTable, $foreignKeyChild, $operator, $primaryKeyParent);
    }

    public function __toString()
    {
        return $this->join ?? '';
    }
}