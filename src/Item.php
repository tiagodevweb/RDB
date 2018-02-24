<?php

declare(strict_types=1);

namespace Tdw\RDB;

use Tdw\RDB\Contract\Item as ItemInterface;

class Item implements ItemInterface
{
    /**
     * @var array
     */
    private $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __get($name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return null;
    }
}
