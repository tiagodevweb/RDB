<?php

declare(strict_types=1);

namespace Tdw\RDB\Statement;

use Tdw\RDB\Contract\Statement\Insert as InsertStatement;
use Tdw\RDB\Contract\Result\Insert as IInsertResult;
use Tdw\RDB\Exception\StatementExecuteException;
use Tdw\RDB\Result\Insert as InsertResult;

class Insert implements InsertStatement
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
     * @var array
     */
    private $parameters;

    public function __construct(\PDO $pdo, string $table, array $parameters)
    {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->parameters = $parameters;
    }

    public function execute(): IInsertResult
    {
        try {
            $stmt = $this->pdo->prepare((string)$this);
            $stmt->execute($this->parameters());
            return new InsertResult($this->pdo, $stmt);
        } catch (\PDOException $e) {
            throw new StatementExecuteException("Execution of insert statement failed", 0, $e);
        }
    }

    public function __toString(): string
    {
        return $this->sql();
    }

    public function parameters(): array
    {
        return array_values($this->parameters);
    }

    private function sql(): string
    {
        $parameters = '';
        for ($i=0; $i < count($this->parameters); $i++) {
            $parameters .= '?, ';
        }
        return sprintf(
            "INSERT INTO %s ( %s ) VALUES ( %s )",
            $this->table,
            implode(', ', array_keys($this->parameters)),
            rtrim($parameters, ', ')
        );
    }
}
