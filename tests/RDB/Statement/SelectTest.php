<?php

namespace Tests\RDB\Statement;

use Tdw\RDB\Statement\Select;

use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase 
{
    private function newInstanceSelectStatement($table = 'employees', array $columns = ['*'])
    {
        return new Select($this->createMock(\PDO::class), $table, $columns);
    }

    /**
     * @group unity
     */
    public function testShouldBeEmptyParametersAndSqlSelectAllFromEmployees()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();

        //act
        $expected = "SELECT * FROM employees";

        //assert
        $this->assertEquals([], $select->parameters());
        $this->assertEquals($expected, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectIdAndNameFromEmployees()
    {
        //arrange
        $select = $this->newInstanceSelectStatement('employees',['id','name']);

        //act
        $expected = "SELECT id, name FROM employees";

        //assert
        $this->assertEquals([], $select->parameters());
        $this->assertEquals($expected, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectIdAndNameFromEmployeesStatesWithMethodColumns()
    {
        //arrange
        $select = $this->newInstanceSelectStatement('employees');
        $select->columns(['id','name']);

        //act
        $expected = "SELECT id, name FROM employees";

        //assert
        $this->assertEquals([], $select->parameters());
        $this->assertEquals($expected, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereId()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $select->where('id', '=', $id);

        //act
        $expectedToString = "SELECT * FROM employees WHERE id = ?";
        $expectedParameters = [$id];

        //assert
        $this->assertEquals($expectedParameters, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdAndDifferentName()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $name = 'Tiago Lopes';
        $select->where( 'id', '=', $id )
               ->where( 'name', '!=', $name );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id = ? AND name != ?";
        $expectedParameters = [$id, $name];

        //assert
        $this->assertEquals($expectedParameters, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdOrDifferentName()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $name = 'Tiago Lopes';
        $select->where( 'id','=',$id )
               ->orWhere( 'name', '!=', $name );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id = ? OR name != ?";
        $expectedParameters = [$id,$name];

        //assert
        $this->assertEquals($expectedParameters, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdIn()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $subSet = [1, 2, 3];
        $select->in( 'id', $subSet );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id IN ( ?, ?, ? )";
        $expectedValues = $subSet;

        //assert
        $this->assertEquals($expectedValues, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdSmallerAndIdIn()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $subSet = [1, 2, 3];
        $select->where( 'id', '<', $id )
               ->in( 'id', $subSet );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id < ? AND id IN ( ?, ?, ? )";
        array_unshift( $subSet, $id );
        $expectedValues = $subSet;

        //assert
        $this->assertEquals($expectedValues, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdSmallerAndIdNotIn()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $subSet = [1, 2, 3];
        $select->where( 'id', '<', $id )
               ->notIn( 'id', $subSet );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id < ? AND id NOT IN ( ?, ?, ? )";
        array_unshift( $subSet, $id );
        $expectedValues = $subSet;

        //assert
        $this->assertEquals($expectedValues, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdSmallerOrIdIn()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $subSet = [1, 2, 3];
        $select->where( 'id', '<', $id )
            ->orIn( 'id', $subSet );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id < ? OR id IN ( ?, ?, ? )";
        array_unshift( $subSet, $id );
        $expectedValues = $subSet;

        //assert
        $this->assertEquals($expectedValues, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdSmallerOrIdNotIn()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $subSet = [1, 2, 3];
        $select->where( 'id', '<', $id )
               ->orNotIn( 'id', $subSet );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id < ? OR id NOT IN ( ?, ?, ? )";
        array_unshift( $subSet, $id );
        $expectedValues = $subSet;

        //assert
        $this->assertEquals($expectedValues, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereNameLike()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $like = 'Tiago%';
        $select->like( 'name', $like );

        //act
        $expectedToString = "SELECT * FROM employees WHERE name LIKE ?";
        $expectedValues = [$like];

        //assert
        $this->assertEquals($expectedValues, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdLargerAndNameLike()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $like = 'Tiago%';
        $select->where( 'id', '>', $id)
               ->like( 'name', $like );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? AND name LIKE ?";
        $expectedValues = [$id, $like];

        //assert
        $this->assertEquals($expectedValues, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdLargerOrNameLike()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $like = 'Tiago%';
        $select->where( 'id', '>', $id)
               ->orLike( 'name', $like );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? OR name LIKE ?";
        $expectedValues = [$id, $like];

        //assert
        $this->assertEquals($expectedValues, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdLargerAndNameNotLike()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $like = 'Tiago%';
        $select->where( 'id', '>', $id)
               ->notLike( 'name', $like );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? AND name NOT LIKE ?";
        $expectedValues = [$id, $like];

        //assert
        $this->assertEquals($expectedValues, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdLargerOrNameNotLike()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $like = 'Tiago%';
        $select->where( 'id', '>', $id)
               ->orNotLike( 'name', $like );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? OR name NOT LIKE ?";
        $expectedValues = [$id, $like];

        //assert
        $this->assertEquals($expectedValues, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereUpdatedNull()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $select->null( 'updated' );

        //act
        $expectedToString = "SELECT * FROM employees WHERE updated IS NULL";
        $expectedValues = [];

        //assert
        $this->assertEquals($expectedValues, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdLargerAndUpdatedNull()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $select->where( 'id', '>', $id)
               ->null( 'updated' );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? AND updated IS NULL";
        $expectedValues = [$id];

        //assert
        $this->assertEquals($expectedValues, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdLargerOrUpdatedNull()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $select->where( 'id', '>', $id)
               ->orNull( 'updated' );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? OR updated IS NULL";
        $expectedValues = [$id];

        //assert
        $this->assertEquals($expectedValues, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdLargerAndUpdatedNotNull()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $select->where( 'id', '>', $id)
               ->notNull( 'updated' );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? AND updated IS NOT NULL";
        $expectedValues = [$id];

        //assert
        $this->assertEquals($expectedValues, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group unity
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdLargerOrUpdatedNotNull()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $select->where( 'id', '>', $id)
               ->orNotNull( 'updated' );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? OR updated IS NOT NULL";
        $expectedValues = [$id];

        //assert
        $this->assertEquals($expectedValues, $select->parameters());
        $this->assertEquals($expectedToString, (string)$select);
    }

    /**
     * @group limit
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdLargerLimit()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $limit = 10;
        $select->where( 'id', '>', $id)
               ->limit($limit);

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? LIMIT ?";
        $expectedValues = [$id,$limit];

        //assert
        $this->assertEquals($expectedToString, (string)$select);
        $this->assertEquals($expectedValues, $select->parameters());
    }

    /**
     * @group limit
     */
    public function testShouldBeAssignedParametersAndSqlSelectAllFromEmployeesWhereIdLargerLimitOffset()
    {
        //arrange
        $select = $this->newInstanceSelectStatement();
        $id = 25;
        $limit = 10;
        $offset = 5;
        $select->where( 'id', '>', $id)
               ->limit($limit, $offset);

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? LIMIT ? OFFSET ?";
        $expectedValues = [$id,$limit,$offset];

        //assert
        $this->assertEquals($expectedToString, (string)$select);
        $this->assertEquals($expectedValues, $select->parameters());
    }
}