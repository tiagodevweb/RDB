<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract\Clause;


interface Relation
{
    public function join(
        string $childTable, string $foreignKeyChild, string $operator,
        string $primaryKeyParent, string $type = 'INNER'
    );
}