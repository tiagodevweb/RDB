<?php

declare(strict_types=1);

namespace Tdw\RDB\Result;

use Tdw\RDB\Collection;
use Tdw\RDB\Contract\Collection as CollectionInterface;
use Tdw\RDB\Contract\Item as ItemInterface;
use Tdw\RDB\Contract\Result\Select as SelectResult;
use Tdw\RDB\Item;

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

    public function fetchAll(): CollectionInterface
    {
        $collection = new Collection($this->statement->fetchAll(\PDO::FETCH_ASSOC));
        return $collection->each(function ($item) { return new Item($item); });
    }

    public function fetch(): ItemInterface
    {
        return new Item($this->statement->fetch(\PDO::FETCH_ASSOC));
    }
}
