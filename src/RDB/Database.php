<?php

declare(strict_types=1);

namespace Tdw\RDB;

use Tdw\RDB\Contract\Database as IDatabase;
use Tdw\RDB\Contract\Result\Select as ISelectResult;
use Tdw\RDB\Exception\DatabaseExecuteException;
use Tdw\RDB\Result\Select as SelectResult;
use Tdw\RDB\Contract\Statement\Delete as DeleteStatement;
use Tdw\RDB\Contract\Statement\Insert as InsertStatement;
use Tdw\RDB\Contract\Statement\Update as UpdateStatement;
use Tdw\RDB\Contract\Statement\Select as SelectStatement;
use Tdw\RDB\Statement\Delete;
use Tdw\RDB\Statement\Insert;
use Tdw\RDB\Statement\Select;
use Tdw\RDB\Statement\Update;

class Database implements IDatabase
{
    /**
     * @var \PDO
     */
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function select(string $table, array $columns = ['*']): SelectStatement
    {
        return new Select($this->pdo, $table, $columns);
    }

    public function insert(string $table, array $parameters): InsertStatement
    {
        return new Insert($this->pdo, $table, $parameters);
    }

    public function update(string $table, array $parameters, array $conditions): UpdateStatement
    {
        return new Update($this->pdo, $table, $parameters, $conditions);
    }

    public function delete(string $table, array $conditions): DeleteStatement
    {
        return new Delete($this->pdo, $table, $conditions);
    }

    public function query(string $sql, array $parameters = []): ISelectResult
    {
        try {
            if (empty($this->parameters)) {
                $stmt = $this->pdo->query($sql);
            } else {
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute($this->parameters);
            }
            return new SelectResult($stmt);
        } catch (\PDOException $e) {
            throw new DatabaseExecuteException("Execution of query database failed", 0, $e);
        }
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }
}
