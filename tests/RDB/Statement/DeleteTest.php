<?php

namespace Tests\RDB\Statement;


use PHPUnit\Framework\TestCase;
use Tdw\RDB\Statement\Delete;

class DeleteTest extends TestCase
{
    /**
     * @group unity-delete
     */
    public function testShouldBeDeleteFromEmployeesWhereId()
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