<?php

declare(strict_types=1);

namespace Tdw\RDB\Contract;

interface Collection extends \IteratorAggregate, \Countable
{
    /**
     * @param $key
     * @param mixed $returnIfNotExists
     * @return mixed
     */
    public function get($key, $returnIfNotExists = null);

    /**
     * @return array
     */
    public function all(): array;

    /**
     * @return bool
     */
    public function isEmpty(): bool;

    /**
     * @param $value
     * @return bool
     */
    public function contains($value): bool;

    /**
     * @return Collection
     */
    public function keys(): Collection;

    /**
     * @return Collection
     */
    public function values(): Collection;

    /**
     * @return mixed
     */
    public function shift();

    /**
     * @return mixed
     */
    public function pop();

    /**
     * @param $value
     * @return mixed
     */
    public function push($value);

    /**
     * @param $value
     * @return mixed
     */
    public function prepend($value);

    /**
     * @param $key
     * @return mixed
     */
    public function remove($key);

    /**
     * @param $value
     * @return mixed
     */
    public function search($value);

    /**
     * @param \Closure $callback
     * @return bool
     */
    public function sort(\Closure $callback): bool;

    /**
     * @param \Closure $callback
     * @return Collection
     */
    public function each(\Closure $callback): Collection;

    /**
     * @param \Closure $callback
     * @return Collection
     */
    public function filter(\Closure $callback): Collection;

    /**
     * @param \Closure $callback
     * @return Collection
     */
    public function map(\Closure $callback): Collection;

    /**
     * @param $key
     * @param $value
     * @return Collection
     */
    public function add($key, $value): Collection;

    /**
     * @return mixed
     */
    public function clear();
}
