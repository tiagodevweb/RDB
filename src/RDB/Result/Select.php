<?php

declare(strict_types=1);

namespace Tdw\RDB\Result;

use Tdw\RDB\Contract\Result\Select as SelectResult;

class Select implements SelectResult
{
    /**
     * @var \PDOStatement
     */
    private $statement;

    public function __construct(\PDOStatement $statement)
    {
        $this->statement = $statement;
    }

    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }

    public function fetchAll(int $style = \PDO::FETCH_ASSOC): array
    {
        return $this->statement->fetchAll($style);
    }

    public function fetch(string $className = \stdClass::class)
    {
        return $this->statement->fetchObject($className);
    }
}