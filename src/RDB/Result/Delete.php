<?php

declare(strict_types=1);

namespace Tdw\RDB\Result;

use Tdw\RDB\Contract\Result\Delete as DeleteResult;

class Delete implements DeleteResult
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
}