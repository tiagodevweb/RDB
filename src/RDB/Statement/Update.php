<?php

declare(strict_types=1);

namespace Tdw\RDB\Statement;

use Tdw\RDB\Contract\Statement\Update as UpdateStatement;
use Tdw\RDB\Contract\Result\Update as IUpdateResult;
use Tdw\RDB\Exception\StatementExecuteException;
use Tdw\RDB\Result\Update as UpdateResult;

class Update implements UpdateStatement
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
    /**
     * @var array
     */
    private $conditions;

    public function __construct(\PDO $pdo, string $table, array $parameters, array $conditions)
    {
        $this->pdo = $pdo;
        $this->table = $table;
        $this->parameters = $parameters;
        $this->conditions = $conditions;
    }

    public function execute(): IUpdateResult
    {
        try {
            $stmt = $this->pdo->prepare((string)$this);
            $stmt->execute($this->parameters());
            return new UpdateResult($stmt);
        } catch (\PDOException $e) {
            throw new StatementExecuteException("Execution of update statement failed", 0, $e);
        }
    }

    public function __toString(): string
    {
        return $this->sql();
    }

    public function parameters(): array
    {
        return array_merge(array_values($this->parameters), array_values($this->conditions));
    }

    private function sql(): string
    {
        return sprintf(
            "UPDATE %s SET %s WHERE %s",
            $this->table,
            $this->createSyntax($this->parameters),
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
