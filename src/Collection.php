<?php

declare(strict_types=1);

namespace Tdw\RDB;

use Tdw\RDB\Contract\Collection as CollectionInterface;

class Collection implements CollectionInterface
{

    /**
     * @var array
     */
    private $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        return $this->items[$key];
    }

    /**
     * @inheritdoc
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * @inheritdoc
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * @inheritdoc
     */
    public function contains($value): bool
    {
        return in_array($value, $this->items);
    }

    /**
     * @inheritdoc
     */
    public function keys(): array
    {
        return array_keys($this->items);
    }

    /**
     * @inheritdoc
     */
    public function values()
    {
        $this->items = array_values($this->items);
    }

    /**
     * @inheritdoc
     */
    public function shift()
    {
        return array_shift($this->items);
    }

    /**
     * @inheritdoc
     */
    public function pop()
    {
        return array_pop($this->items);
    }

    /**
     * @inheritdoc
     */
    public function push($value)
    {
        $this->items[] = $value;
    }

    /**
     * @inheritdoc
     */
    public function prepend($value)
    {
        array_unshift($this->items, $value);
    }

    /**
     * @inheritdoc
     */
    public function remove($key)
    {
        unset($this->items[$key]);
    }

    /**
     * @inheritdoc
     */
    public function search($value)
    {
        return array_search($value, $this->items, true);
    }

    /**
     * @inheritdoc
     */
    public function sort(\Closure $callback)
    {
        uasort($this->items, $callback);
    }

    /**
     * @inheritdoc
     */
    public function each(\Closure $callback)
    {
        array_map($callback, $this->items);
    }

    /**
     * @inheritdoc
     */
    public function filter(\Closure $callback): CollectionInterface
    {
        return new Collection(array_filter($this->items, $callback));
    }

    /**
     * @inheritdoc
     */
    public function clear()
    {
        $this->items = [];
    }

    /**
     * @inheritdoc
     */
    public function map(\Closure $callback): CollectionInterface
    {
        return new Collection(array_map($callback, $this->items, array_keys($this->items)));
    }

    /**
     * @inheritdoc
     */
    public function add($key, $value): CollectionInterface
    {
        $this->items[$key] = $value;
        return $this;
    }

    /**
     * @see \IteratorAggregate::getIterator()
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @see \Countable::count()
     */
    public function count()
    {
        return count($this->items);
    }
}
