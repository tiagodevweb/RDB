<?php

declare( strict_types=1 );

namespace Tdw\RDB\Contract;

use Tdw\RDB\Contract\Result\Select as SelectResult;
use Tdw\RDB\Contract\Statement\Delete;
use Tdw\RDB\Contract\Statement\Insert;
use Tdw\RDB\Contract\Statement\Select;
use Tdw\RDB\Contract\Statement\Update;

interface Database
{
    public function select(string $table, array $columns = ['*']): Select;
    public function insert(string $table, array $parameters): Insert;
    public function update(string $table, array $parameters, array $conditions): Update;
    public function delete(string $table, array $conditions): Delete;
    public function query(string $sql, array $parameters = []): SelectResult;
    public function beginTransaction(): bool;
    public function commit(): bool;
    public function rollBack(): bool;
    public function exec(string $sql): int;
}