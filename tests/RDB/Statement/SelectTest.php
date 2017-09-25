<?php

namespace Tests\RDB\Statement;

use Tdw\RDB\Statement\Select;

use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase 
{
    /**
     * @var Select
     */
    private $select;

    protected function setUp()
    {
        parent::setUp();
        $this->select = new Select($this->createMock(\PDO::class), 'employees', ['*']);
    }

    /**
     * @group unitary-select
     */
    public function testShouldBeEmptyParametersAndSelectAllFromEmployees()
    {
        //arrange

        //act
        $expected = "SELECT * FROM employees";

        //assert
        $this->assertEquals([], $this->select->parameters());
        $this->assertEquals($expected, (string)$this->select);
    }

    /**
     * @group unitary-select
     */
    public function testShouldBeAssignedParametersAndSelectIdAndNameFromEmployees()
    {
        //arrange
        $this->select->columns(['id, name']);

        //act
        $expected = "SELECT id, name FROM employees";

        //assert
        $this->assertEquals([], $this->select->parameters());
        $this->assertEquals($expected, (string)$this->select);
    }

    /**
     * @group unitary-select
     */
    public function testShouldBeAssignedParametersAndSelectIdAndNameFromEmployeesStatesWithMethodColumns()
    {
        //arrange
        $this->select->columns(['id','name']);

        //act
        $expected = "SELECT id, name FROM employees";

        //assert
        $this->assertEquals([], $this->select->parameters());
        $this->assertEquals($expected, (string)$this->select);
    }

    /**
     * @group unitary-select-join
     */
    public function testShouldBeAssignedParametersAndSelectAllFromEmployeesJoinUsers()
    {
        //arrange
        $this->select->join('users','users.id','=','employees.user_id');

        //act
        $expectedToString = "SELECT * FROM employees INNER JOIN users ON (users.id = employees.user_id)";
        $expectedParameters = [];

        //assert
        $this->assertEquals($expectedToString, (string)$this->select);
        $this->assertEquals($expectedParameters, $this->select->parameters());
    }

    /**
     * @group unitary-select-join
     */
    public function testShouldBeAssignedParametersAndSelectAllFromEmployeesLeftJoinUsers()
    {
        //arrange
        $this->select->leftJoin('users','users.id','=','employees.user_id');

        //act
        $expectedToString = "SELECT * FROM employees LEFT OUTER JOIN users ON (users.id = employees.user_id)";
        $expectedParameters = [];

        //assert
        $this->assertEquals($expectedToString, (string)$this->select);
        $this->assertEquals($expectedParameters, $this->select->parameters());
    }

    /**
     * @group unitary-select-join
     */
    public function testShouldBeAssignedParametersAndSelectAllFromEmployeesRightJoinUsers()
    {
        //arrange
        $this->select->rightJoin('users','users.id','=','employees.user_id');

        //act
        $expectedToString = "SELECT * FROM employees RIGHT OUTER JOIN users ON (users.id = employees.user_id)";
        $expectedParameters = [];

        //assert
        $this->assertEquals($expectedToString, (string)$this->select);
        $this->assertEquals($expectedParameters, $this->select->parameters());
    }

    /**
     * @group unitary-select-join
     */
    public function testShouldBeAssignedParametersAndSelectAllFromEmployeesFullJoinUsers()
    {
        //arrange
        $this->select->fullJoin('users','users.id','=','employees.user_id');

        //act
        $expectedToString = "SELECT * FROM employees FULL OUTER JOIN users ON (users.id = employees.user_id)";
        $expectedParameters = [];

        //assert
        $this->assertEquals($expectedToString, (string)$this->select);
        $this->assertEquals($expectedParameters, $this->select->parameters());
    }

    /**
     * @group unitary-select-where
     */
    public function testShouldBeAssignedParametersAndSelectWithWhere()
    {
        //arrange
        $id = 25;
        $this->select->where('id', '=', $id);

        //act
        $expectedToString = "SELECT * FROM employees WHERE id = ?";
        $expectedParameters = [$id];

        //assert
        $this->assertEquals($expectedParameters, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-where
     */
    public function testShouldBeAssignedParametersAndSelectWithMultipleWhere()
    {
        //arrange
        $id = 25;
        $name = 'Tiago Lopes';
        $this->select->where( 'id', '=', $id )
                     ->where( 'name', '!=', $name );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id = ? AND name != ?";
        $expectedParameters = [$id, $name];

        //assert
        $this->assertEquals($expectedParameters, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-where
     */
    public function testShouldBeAssignedParametersAndSelectWithMultipleOrWhere()
    {
        //arrange
        $id = 25;
        $name = 'Tiago Lopes';
        $this->select->where('id','=',$id)
                     ->orWhere('name', '!=', $name);

        //act
        $expectedToString = "SELECT * FROM employees WHERE id = ? OR name != ?";
        $expectedParameters = [$id,$name];

        //assert
        $this->assertEquals($expectedParameters, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-between
     */
    public function testShouldBeAssignedParametersAndSelectWithBetween()
    {
        //arrange
        $valueOne = 25;
        $valueTwo = 50;
        $this->select->between('id', $valueOne, $valueTwo);

        //act
        $expectedToString = "SELECT * FROM employees WHERE id BETWEEN ? AND ?";
        $expectedParameters = [$valueOne, $valueTwo];

        //assert
        $this->assertEquals($expectedParameters, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-between
     */
    public function testShouldBeAssignedParametersAndSelectWithNotBetween()
    {
        //arrange
        $valueOne = 25;
        $valueTwo = 50;
        $this->select->notBetween('id', $valueOne, $valueTwo);

        //act
        $expectedToString = "SELECT * FROM employees WHERE id NOT BETWEEN ? AND ?";
        $expectedParameters = [$valueOne, $valueTwo];

        //assert
        $this->assertEquals($expectedParameters, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-between
     */
    public function testShouldBeAssignedParametersAndSelectWithOrBetween()
    {
        //arrange
        $id = 100;
        $valueOne = 25;
        $valueTwo = 50;
        $this->select->where('id','>',$id)
                     ->orBetween('id', $valueOne, $valueTwo);

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? OR id BETWEEN ? AND ?";
        $expectedParameters = [$id, $valueOne, $valueTwo];

        //assert
        $this->assertEquals($expectedParameters, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-between
     */
    public function testShouldBeAssignedParametersAndSelectWithOrNotBetween()
    {
        //arrange
        $id = 100;
        $valueOne = 25;
        $valueTwo = 50;
        $this->select->where('id','>',$id)
                     ->orNotBetween('id', $valueOne, $valueTwo);

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? OR id NOT BETWEEN ? AND ?";
        $expectedParameters = [$id, $valueOne, $valueTwo];

        //assert
        $this->assertEquals($expectedParameters, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-in
     */
    public function testShouldBeAssignedParametersAndSelectWithIn()
    {
        //arrange
        $subSet = [1, 2, 3];
        $this->select->in('id', $subSet);

        //act
        $expectedToString = "SELECT * FROM employees WHERE id IN ( ?, ?, ? )";
        $expectedValues = $subSet;

        //assert
        $this->assertEquals($expectedValues, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-in
     */
    public function testShouldBeAssignedParametersAndSelectWithNotIn()
    {
        //arrange
        $id = 25;
        $subSet = [1, 2, 3];
        $this->select->where( 'id', '>', $id )
                     ->notIn('id', $subSet);

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? AND id NOT IN ( ?, ?, ? )";
        array_unshift( $subSet, $id );
        $expectedValues = $subSet;

        //assert
        $this->assertEquals($expectedValues, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-in
     */
    public function testShouldBeAssignedParametersAndSelectWithOrIn()
    {
        //arrange
        $id = 25;
        $subSet = [1, 2, 3];
        $this->select->where( 'id', '<', $id )
            ->orIn( 'id', $subSet );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id < ? OR id IN ( ?, ?, ? )";
        array_unshift( $subSet, $id );
        $expectedValues = $subSet;

        //assert
        $this->assertEquals($expectedValues, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-in
     */
    public function testShouldBeAssignedParametersAndSelectOrNotIn()
    {
        //arrange
        $id = 25;
        $subSet = [1, 2, 3];
        $this->select->where( 'id', '<', $id )
                     ->orNotIn( 'id', $subSet );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id < ? OR id NOT IN ( ?, ?, ? )";
        array_unshift( $subSet, $id );
        $expectedValues = $subSet;

        //assert
        $this->assertEquals($expectedValues, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-like
     */
    public function testShouldBeAssignedParametersAndSelectWithLike()
    {
        //arrange
        $like = 'Tiago%';
        $this->select->like( 'name', $like );

        //act
        $expectedToString = "SELECT * FROM employees WHERE name LIKE ?";
        $expectedValues = [$like];

        //assert
        $this->assertEquals($expectedValues, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-like
     */
    public function testShouldBeAssignedParametersAndSelectWithOrLike()
    {
        //arrange
        $id = 25;
        $like = 'Tiago%';
        $this->select->where( 'id', '>', $id)
               ->orLike( 'name', $like );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? OR name LIKE ?";
        $expectedValues = [$id, $like];

        //assert
        $this->assertEquals($expectedValues, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-like
     */
    public function testShouldBeAssignedParametersAndSelectWithNotLike()
    {
        //arrange
        $id = 25;
        $like = 'Tiago%';
        $this->select->where( 'id', '>', $id)
               ->notLike( 'name', $like );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? AND name NOT LIKE ?";
        $expectedValues = [$id, $like];

        //assert
        $this->assertEquals($expectedValues, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-like
     */
    public function testShouldBeAssignedParametersAndSelectAllWithOrNotLike()
    {
        //arrange
        $id = 25;
        $like = 'Tiago%';
        $this->select->where('id', '>', $id)
                     ->orNotLike('name', $like);

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? OR name NOT LIKE ?";
        $expectedValues = [$id, $like];

        //assert
        $this->assertEquals($expectedValues, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-null
     */
    public function testShouldBeAssignedParametersAndSelectWithIsNull()
    {
        //arrange
        $this->select->null('updated');

        //act
        $expectedToString = "SELECT * FROM employees WHERE updated IS NULL";
        $expectedValues = [];

        //assert
        $this->assertEquals($expectedValues, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-null
     */
    public function testShouldBeAssignedParametersAndSelectWithOrIsNull()
    {
        //arrange
        $id = 25;
        $this->select->where('id', '>', $id)
                     ->orNull('updated');

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? OR updated IS NULL";
        $expectedValues = [$id];

        //assert
        $this->assertEquals($expectedValues, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-null
     */
    public function testShouldBeAssignedParametersAndSelectWithNotNull()
    {
        //arrange
             $this->select->notNull( 'updated' );

        //act
        $expectedToString = "SELECT * FROM employees WHERE updated IS NOT NULL";
        $expectedValues = [];

        //assert
        $this->assertEquals($expectedValues, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-null
     */
    public function testShouldBeAssignedParametersAndSelectWithOrNotNull()
    {
        //arrange
        $id = 25;
        $this->select->where( 'id', '>', $id)
               ->orNotNull( 'updated' );

        //act
        $expectedToString = "SELECT * FROM employees WHERE id > ? OR updated IS NOT NULL";
        $expectedValues = [$id];

        //assert
        $this->assertEquals($expectedValues, $this->select->parameters());
        $this->assertEquals($expectedToString, (string)$this->select);
    }

    /**
     * @group unitary-select-limit
     */
    public function testShouldBeAssignedParametersAndSelectWithLimit()
    {
        //arrange
           $limit = 10;
        $this->select->limit($limit);

        //act
        $expectedToString = "SELECT * FROM employees LIMIT ?";
        $expectedValues = [$limit];

        //assert
        $this->assertEquals($expectedToString, (string)$this->select);
        $this->assertEquals($expectedValues, $this->select->parameters());
    }

    /**
     * @group unitary-select-limit
     */
    public function testShouldBeAssignedParametersAndSelectWithLimitAndOffset()
    {
        //arrange
        $limit = 10;
        $offset = 5;
        $this->select->limit($limit, $offset);

        //act
        $expectedToString = "SELECT * FROM employees LIMIT ? OFFSET ?";
        $expectedValues = [$limit,$offset];

        //assert
        $this->assertEquals($expectedToString, (string)$this->select);
        $this->assertEquals($expectedValues, $this->select->parameters());
    }
}