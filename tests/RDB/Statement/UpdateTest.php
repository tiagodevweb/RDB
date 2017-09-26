<?php

namespace Tests\RDB\Statement;

use PHPUnit\Framework\TestCase;
use Tdw\RDB\Statement\Update;

class UpdateTest extends TestCase
{
    /**
     * @group unity-update
     */
    public function testShouldBeUpdateEmployeesNameEmailWhereId()
    {
        //arrange
        $name = 'Name Test';
        $email = 'test@email.com';
        $id = 25;
        $update = new Update(
            $this->createMock(\PDO::class),
            'employees',
            ['name' => $name,'email' => $email],
            ['id' => $id]
        );

        //act
        $expectedToString = "UPDATE employees SET name = ?, email = ? WHERE id = ?";

        //assert
        $this->assertEquals([$name,$email,$id], $update->parameters());
        $this->assertEquals($expectedToString, (string)$update);
    }
}
