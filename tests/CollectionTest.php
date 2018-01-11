<?php

declare(strict_types=1);

namespace Tests\RDB;

use PHPUnit\Framework\TestCase;
use Tdw\RDB\Collection;

class CollectionTest extends TestCase
{
    private $items = ['George', 'Jack', 'James', 'Olivia', 'Sophie'];

    /**
     * @var Collection
     */
    private $collection;

    public function setUp()
    {
        $this->collection = new Collection($this->items);
    }

    public function test_should_get_item_by_key()
    {
        $this->assertEquals('James', $this->collection->get(2));
    }

    public function test_should_get_all_items()
    {
        $this->assertEquals($this->items, $this->collection->all());
    }

    public function test_should_check_for_item()
    {
        $this->assertTrue($this->collection->contains('Jack'));
    }

    public function test_should_return_the_item_keys()
    {
        $keys = $this->collection->keys();

        $this->assertEquals([0,1,2,3,4], $keys);
    }

    public function test_should_get_and_remove_the_first_item()
    {
        $person = $this->collection->shift();

        $this->assertCount(4, $this->collection);
        $this->assertEquals('George', $person);
    }

    public function test_should_pop_the_last_item()
    {
        $person = $this->collection->pop();

        $this->assertCount(4, $this->collection);
        $this->assertEquals('Sophie', $person);
    }

    public function test_should_prepend_the_collection()
    {
        $name = 'Abe';
        $this->collection->prepend($name);

        $this->assertCount(6, $this->collection);
        $this->assertEquals($name, $this->collection->get(0));
    }

    public function test_should_push_item_onto_the_end()
    {
        $name = 'John';
        $this->collection->push($name);

        $this->assertEquals(6, $this->collection->count());
        $this->assertEquals($name, $this->collection->get(5));
    }

    public function test_should_remove_item()
    {
        $this->collection->remove(0);
        $this->assertCount(4, $this->collection);
    }

    public function test_should_search_the_collection()
    {
        $key = $this->collection->search('Olivia');

        $this->assertEquals(3, $key);
    }

    public function test_should_sort_items_in_collection_and_reset_keys()
    {
        $this->collection->sort(function ($a, $b) {
            if ($a == $b) return 0;

            return ($a < $b) ? -1 : 1;
        });

        $this->collection->values();

        $this->assertEquals('George', $this->collection->get(0));
        $this->assertEquals('Jack', $this->collection->get(1));
        $this->assertEquals('James', $this->collection->get(2));
        $this->assertEquals('Olivia', $this->collection->get(3));
        $this->assertEquals('Sophie', $this->collection->get(4));
    }

    public function test_should_run_callback_on_each_item()
    {
        $this->collection->each(function ($person) {
            $this->assertTrue(is_string($person));
        });
    }

    public function test_should_filter_the_collection()
    {
        $filtered = $this->collection->filter(function ($person) {
            return substr($person, 0,1) === 'J';
        });

        $this->assertEquals(2, $filtered->count());
    }

    public function test_should_map_each_item()
    {
        $family = $this->collection->map(function ($person) {
            return "$person Smith";
        });

        $this->assertEquals('George Smith', $family->get(0));
        $this->assertEquals('Jack Smith', $family->get(1));
        $this->assertEquals('James Smith', $family->get(2));
        $this->assertEquals('Olivia Smith', $family->get(3));
        $this->assertEquals('Sophie Smith', $family->get(4));
    }

    public function test_should_add_item()
    {
        $value = 'Jessica';
        $key = 5;
        $this->collection->add($key, $value);

        $this->assertEquals($value, $this->collection->get($key));
    }

    public function test_should_clear_collection()
    {
        $this->collection->clear();
        $this->assertCount(0, $this->collection);
    }

    public function test_should_empty_collection()
    {
        $this->collection->clear();
        $this->assertTrue($this->collection->isEmpty());
    }
}
