<?php

namespace Tests\RDB\Statement;


use PHPUnit\Framework\TestCase;
use Tdw\RDB\Statement\Insert;

class InsertTest extends TestCase
{
    /**
     * @group unity
     */
    public function testShouldBeInsertIntoEmployeesNameEmail()
    {
        //arrange
        $name = 'Name Test';
        $email = 'test@email.com';
        $insert = new Insert(
            $this->createMock(\PDO::class),
            'employees',
            ['name' => $name,'email' => $email]
        );

        //act
        $expectedToString = "INSERT INTO employees ( name, email ) VALUES ( ?, ? )";

        //assert
        $this->assertEquals([$name,$email], $insert->parameters());
        $this->assertEquals($expectedToString, (string)$insert);
    }
}