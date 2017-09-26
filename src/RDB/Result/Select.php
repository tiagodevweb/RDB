<?php

declare(strict_types=1);

namespace Tdw\RDB\Result;

use Tdw\RDB\Contract\Result\Select as SelectResult;

class Select implements SelectResult
{
    const TO_ARRAY = 2;
    const TO_OBJECT = 5;
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

    public function fetchAll(int $style = self::TO_ARRAY): array
    {
        return $this->statement->fetchAll($style);
    }

    public function fetch(int $style = self::TO_ARRAY)
    {
        return $this->statement->fetch($style);
    }
}
