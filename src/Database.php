<?php

declare(strict_types=1);

namespace Tdw\RDB;

use Tdw\RDB\Exception\DatabaseExecuteException;
use Tdw\RDB\Result\Select as SelectResult;
use Tdw\RDB\Contract\Database as DatabaseInterface;
use Tdw\RDB\Contract\Result\Select as ISelectResult;
use Tdw\RDB\Contract\Statement\Delete as DeleteStatement;
use Tdw\RDB\Contract\Statement\Insert as InsertStatement;
use Tdw\RDB\Contract\Statement\Update as UpdateStatement;
use Tdw\RDB\Contract\Statement\Select as SelectStatement;
use Tdw\RDB\Statement\Delete;
use Tdw\RDB\Statement\Insert;
use Tdw\RDB\Statement\Select;
use Tdw\RDB\Statement\Update;

class Database implements DatabaseInterface
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

    public function selectSQL(string $sql, array $parameters = []): ISelectResult
    {
        try {
            return new SelectResult($this->queryOrPrepare($sql, $parameters));
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

    /**
     * @param string $sql
     * @param array $parameters
     * @return \PDOStatement
     */
    private function queryOrPrepare(string $sql, array $parameters): \PDOStatement
    {
        if (sizeof($parameters) === 0) {
            return $this->pdo->query($sql);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($parameters);
        return $stmt;
    }
}
