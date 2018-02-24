<?php

namespace Tests\RDB\Statement;

use PHPUnit\Framework\TestCase;
use Tdw\RDB\Statement\Delete;

class DeleteTest extends TestCase
{
    /**
     * @test
     */
    public function test_should_be_delete_from_employees_where_id()
    {
        //arrange
        $id = 25;
        $delete = new Delete(
            $this->createMock(\PDO::class),
            'employees',
            ['id' => $id]
        );

        //act
        $expectedToString = "DELETE FROM employees WHERE id = ?";

        //assert
        $this->assertEquals([$id], $delete->parameters());
        $this->assertEquals($expectedToString, (string)$delete);
    }
}
