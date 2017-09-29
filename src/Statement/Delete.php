<?php

declare(strict_types=1);

namespace Tdw\RDB\Statement;

use Tdw\RDB\Contract\Statement\Delete as DeleteStatement;
use Tdw\RDB\Contract\Result\Delete as IDeleteResult;
use Tdw\RDB\Exception\StatementExecuteException;
use Tdw\RDB\Result\Delete as DeleteResult;

class Delete implements DeleteStatement
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
    private $conditions;

    public function __construct(\PDO $pdo, string $table, array $conditions)
    {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->conditions = $conditions;
    }

    public function execute(): IDeleteResult
    {
        try {
            $stmt = $this->pdo->prepare((string)$this);
            $stmt->execute($this->parameters());
            return new DeleteResult($stmt);
        } catch (\PDOException $e) {
            throw new StatementExecuteException("Execution of delete statement failed", 0, $e);
        }
    }

    public function __toString(): string
    {
        return $this->sql();
    }

    public function parameters(): array
    {
        return array_values($this->conditions);
    }

    private function sql(): string
    {
        return sprintf(
            "DELETE FROM %s WHERE %s",
            $this->table,
            $this->createSyntax($this->conditions)
        );
    }

    private function createSyntax(array $data): string
    {
        $string = '';
        foreach ($data as $key => $value) {
            $string .= "{$key} = ?, ";
        }
        return rtrim($string, ', ');
    }
}
