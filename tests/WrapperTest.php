<?php

namespace Tests\RDB;

use PHPUnit\Framework\TestCase;
use Tdw\RDB\Wrapper;

class WrapperTest extends TestCase
{
    /**
     * @test
     */
    public function should_return_exception_for_undefined_db_driver()
    {
        $RDB = new Wrapper([
            'db_host' => 'localhost',
            'db_port' => 'port',
            'db_name' => 'name',
            'db_user' => 'user',
            'db_pass' => 'pass'
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Key db_driver not found');

        $RDB->getDatabase();
    }

    /**
     * @test
     */
    public function should_return_exception_for_undefined_db_host()
    {
        $RDB = new Wrapper([
            'db_driver' => 'driver',
            'db_port' => 'port',
            'db_name' => 'name',
            'db_user' => 'user',
            'db_pass' => 'pass'
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Key db_host not found');

        $RDB->getDatabase();
    }

    /**
     * @test
     */
    public function should_return_exception_for_undefined_db_port()
    {
        $RDB = new Wrapper([
            'db_driver' => 'driver',
            'db_host' => 'localhost',
            'db_name' => 'name',
            'db_user' => 'user',
            'db_pass' => 'pass'
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Key db_port not found');

        $RDB->getDatabase();
    }

    /**
     * @test
     */
    public function should_return_exception_for_undefined_db_name()
    {
        $RDB = new Wrapper([
            'db_driver' => 'driver',
            'db_host' => 'localhost',
            'db_port' => 'port',
            'db_user' => 'user',
            'db_pass' => 'pass'
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Key db_name not found');

        $RDB->getDatabase();
    }

    /**
     * @test
     */
    public function should_return_exception_for_undefined_db_user()
    {
        $RDB = new Wrapper([
            'db_driver' => 'driver',
            'db_host' => 'localhost',
            'db_port' => 'port',
            'db_name' => 'name',
            'db_pass' => 'pass'
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Key db_user not found');

        $RDB->getDatabase();
    }

    /**
     * @test
     */
    public function should_return_exception_for_undefined_db_pass()
    {
        $RDB = new Wrapper([
            'db_driver' => 'driver',
            'db_host' => 'localhost',
            'db_port' => 'port',
            'db_name' => 'name',
            'db_user' => 'user'
        ]);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Key db_pass not found');

        $RDB->getDatabase();
    }
}