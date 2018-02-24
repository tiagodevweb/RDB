<?php

namespace Tests\RBD;

use PHPUnit\Framework\TestCase;
use Tdw\RDB\Item;

class ItemTest extends TestCase
{
    /**
    * @test
    */
    public function a_item_can_return_key()
    {
        $data = ['name' => 'Joe', 'age' => 25];

        $item = new Item($data);

        $this->assertEquals('Joe', $item->name);
        $this->assertEquals(25, $item->age);
        $this->assertNull($item->xpto);
    }
}