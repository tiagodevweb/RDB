<?php

declare(strict_types=1);

namespace Tdw\RDB\Clause;

use Tdw\RDB\Contract\Clause\Relation as IRelation;

class Relation implements IRelation
{
    private $joins = [];

    public function join(
        string $childTable,
        string $foreignKeyChild,
        string $operator,
        string $primaryKeyParent,
        string $type = 'INNER'
    ) {
        $this->joins[] = sprintf(
            " %s JOIN %s ON (%s %s %s)",
            $type,
            $childTable,
            $foreignKeyChild,
            $operator,
            $primaryKeyParent
        );
    }

    public function __toString()
    {
        return empty($this->joins) ? '' : $this->concatJoin();
    }

    private function concatJoin(): string
    {
        return ' ' . ltrim(implode('', $this->joins), ' ');
    }
}
