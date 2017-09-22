<?php

declare(strict_types=1);

namespace Tdw\RDB\Result;

use Tdw\RDB\Contract\Result\Insert as InsertResult;

class Insert implements InsertResult
{
    /**
     * @var \PDO
     */
    private $pdo;
    /**
     * @var \PDOStatement
     */
    private $statement;

    public function __construct(\PDO $pdo, \PDOStatement $statement)
    {
        $this->pdo = $pdo;
        $this->statement = $statement;
    }

    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }

    public function lastInsertId(string $name = null): int
    {
        return (int)$this->pdo->lastInsertId($name);
    }
}