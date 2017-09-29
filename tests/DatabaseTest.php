<?php

namespace Tests\RDB;

use PHPUnit\Framework\TestCase;
use Tdw\RDB\Database;
use Tdw\RDB\Result\Insert as InsertResult;

class DatabaseTest extends TestCase
{
    /**
     * @var \PDO
     */
    private static $pdo;

    /**
     * @var Database
     */
    private $database;

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$pdo = new \PDO('sqlite::memory:');
        self::$pdo->exec(
            "CREATE TABLE categories(cat_id INTEGER PRIMARY KEY AUTOINCREMENT, name STRING)"
        );
        self::$pdo->exec(
            "CREATE TABLE posts (
                      id INTEGER PRIMARY KEY AUTOINCREMENT,
                      title STRING,
                      description STRING,
                      visited INTEGER DEFAULT 0,
                      category_id INTEGER NULL,
                    FOREIGN KEY(category_id) REFERENCES categories(cat_id))"
        );
    }

    public static function tearDownAfterClass()
    {
        self::$pdo->exec("DROP TABLE posts");
        parent::tearDownAfterClass();
    }

    public function setUp()
    {
        parent::setUp();
        $this->database = new Database(self::$pdo);
        $this->database->beginTransaction();
    }

    public function tearDown()
    {
        $this->database->rollBack();
        $this->database = null;
        parent::tearDown();
    }

    /**
     * @group integration-database-insert
     */
    public function testShouldInsertRowInDatabase()
    {
        //arrange
        $parameters = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test'
        ];
        $insertStatement = $this->database->insert('posts', $parameters);
        /**@var \Tdw\RDB\Result\Insert $result */
        $result = $insertStatement->execute();

        //act
        $rowCount = 1;

        //assert
        $this->assertEquals($rowCount, $result->rowCount());
        $this->assertTrue(is_int($result->lastInsertId()));
    }

    /**
     * @group integration-database-select
     */
    public function testShouldReturnDataExpected()
    {
        //arrange
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test'
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2'
        ];
        $this->insertRowAndReturnInsertResult('posts', $post);
        $this->insertRowAndReturnInsertResult('posts', $post2);
        $selectStatement = $this->database->select('posts', ['title','description']);
        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();
        //act
        $expected = [$post,$post2];
        $actual = $result->fetchAll();
        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-where
     */
    public function testShouldReturnDataExpectedWithWhere()
    {
        //arrange
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test'
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2'
        ];
        /**@var \Tdw\RDB\Result\Insert $resultInsert */
        $this->insertRowAndReturnInsertResult('posts', $post2);
        $resultInsert = $this->insertRowAndReturnInsertResult('posts', $post);
        $selectStatement = $this->database->select('posts', ['title','description']);
        $selectStatement->where('id', '=', $resultInsert->lastInsertId());
        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = $post;
        $actual = $result->fetch();

        //assert
        $this->assertArraySubset($expected, $actual);
    }

    /**
     * @group integration-database-select-where
     * @expectedException \Tdw\RDB\Exception\StatementExecuteException
     * @expectedExceptionMessage The where clause should be called before Tdw\RDB\Statement\Select::orWhere
     */
    public function testShouldThrowAnExceptionInOrWhere()
    {
        //arrange
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test'
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2'
        ];
        /**@var \Tdw\RDB\Result\Insert $resultInsert */
        $this->insertRowAndReturnInsertResult('posts', $post2);
        $resultInsert = $this->insertRowAndReturnInsertResult('posts', $post);
        $selectStatement = $this->database->select('posts', ['title','description']);
        $selectStatement->orWhere('id', '=', $resultInsert->lastInsertId());
    }

    /**
     * @group integration-database-select-where
     */
    public function testShouldReturnDataExpectedWithWhereOrWhere()
    {
        //arrange
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test'
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2'
        ];
        /**@var \Tdw\RDB\Result\Insert $resultInsert */
        $this->insertRowAndReturnInsertResult('posts', $post2);
        $lastInsertId = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $selectStatement = $this->database->select('posts', ['title','description']);
        $selectStatement->where('id', '=', $lastInsertId)
            ->orWhere('title', '=', 'Title RDB Test 2');

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = [$post2, $post];
        $actual = $result->fetchAll();

        //assert
        $this->assertArraySubset($expected, $actual);
    }

    /**
     * @group integration-database-select-join
     */
    public function testShouldReturnDataExpectedWithJoin()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test',
            'category_id' => $lastInsertIdCat
        ];
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $selectStatement = $this->database->select('posts')
            ->columns(['name','title','description','category_id'])
            ->join('categories', 'cat_id', '=', 'category_id')
            ->where('id', '=', $lastInsertIdPost);

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = array_merge($category, $post);
        $actual = $result->fetch();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-join
     */
    public function testShouldReturnDataExpectedWithJoinAndAlias()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test',
            'category_id' => $lastInsertIdCat
        ];
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $selectStatement = $this->database->select('posts p')
            ->columns(['c.name','p.title'])
            ->join('categories c', 'c.cat_id', '=', 'p.category_id')
            ->where('p.id', '=', $lastInsertIdPost);

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        unset($post['description']);
        unset($post['category_id']);
        $expected = array_merge($category, $post);
        $actual = $result->fetch();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-between
     */
    public function testShouldReturnDataExpectedWithBetween()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test',
            'visited' => 100,
            'category_id' => $lastInsertIdCat
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2',
            'visited' => 200,
            'category_id' => $lastInsertIdCat
        ];
        $this->insertRowAndReturnInsertResult('posts', $post2);
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $selectStatement = $this->database->select('posts', ['id'])
                ->between('visited', 50, 150);

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = [['id'=>$lastInsertIdPost]];
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-between
     * @expectedException \Tdw\RDB\Exception\StatementExecuteException
     * @expectedExceptionMessage The where clause should be called before Tdw\RDB\Statement\Select::orBetween
     */
    public function testShouldThrowAnExceptionInOrBetween()
    {
        //arrange
        $this->database->select('posts', ['id'])->orBetween('visited', 50, 150);
    }

    /**
     * @group integration-database-select-between
     */
    public function testShouldReturnDataExpectedWithNotBetween()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test',
            'visited' => 100,
            'category_id' => $lastInsertIdCat
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2',
            'visited' => 200,
            'category_id' => $lastInsertIdCat
        ];
        $this->insertRowAndReturnInsertResult('posts', $post2);
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $selectStatement = $this->database->select('posts', ['id'])
                      ->notBetween('visited', 150, 250);

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = [['id'=>$lastInsertIdPost]];
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-between
     * @expectedException \Tdw\RDB\Exception\StatementExecuteException
     * @expectedExceptionMessage The where clause should be called before Tdw\RDB\Statement\Select::orNotBetween
     */
    public function testShouldThrowAnExceptionInOrNotBetween()
    {
        //arrange
        $this->database->select('posts', ['id'])->orNotBetween('visited', 150, 250);
    }

    /**
     * @group integration-database-select-in
     */
    public function testShouldReturnDataExpectedWithIn()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test',
            'visited' => 100,
            'category_id' => $lastInsertIdCat
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2',
            'visited' => 200,
            'category_id' => $lastInsertIdCat
        ];
        $this->insertRowAndReturnInsertResult('posts', $post2);
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $selectStatement = $this->database->select('posts', ['id'])
            ->in('visited', [100,101,102]);

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = [['id'=>$lastInsertIdPost]];
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-in
     * @expectedException \Tdw\RDB\Exception\StatementExecuteException
     * @expectedExceptionMessage The where clause should be called before Tdw\RDB\Statement\Select::orIn
     */
    public function testShouldThrowAnExceptionInOrIn()
    {
        //arrange
        $this->database->select('posts', ['id'])->orIn('visited', [100,101,102]);
    }

    /**
     * @group integration-database-select-in
     */
    public function testShouldReturnDataExpectedWithNotIn()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test',
            'visited' => 100,
            'category_id' => $lastInsertIdCat
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2',
            'visited' => 200,
            'category_id' => $lastInsertIdCat
        ];
        $this->insertRowAndReturnInsertResult('posts', $post2);
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $selectStatement = $this->database->select('posts', ['id'])
            ->notIn('visited', [200,201,202]);

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = [['id'=>$lastInsertIdPost]];
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-in
     * @expectedException \Tdw\RDB\Exception\StatementExecuteException
     * @expectedExceptionMessage The where clause should be called before Tdw\RDB\Statement\Select::orNotIn
     */
    public function testShouldThrowAnExceptionInOrNotIn()
    {
        //arrange
        $this->database->select('posts', ['id'])->orNotIn('visited', [200,201,202]);
    }

    /**
     * @group integration-database-select-like
     */
    public function testShouldReturnDataExpectedWithLike()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB TestLike',
            'description' => 'Description RDB Test',
            'visited' => 100,
            'category_id' => $lastInsertIdCat
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2',
            'visited' => 200,
            'category_id' => $lastInsertIdCat
        ];
        $this->insertRowAndReturnInsertResult('posts', $post2);
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $selectStatement = $this->database->select('posts', ['id'])
            ->like('title', '%TestLike');

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = [['id'=>$lastInsertIdPost]];
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-like
     * @expectedException \Tdw\RDB\Exception\StatementExecuteException
     * @expectedExceptionMessage The where clause should be called before Tdw\RDB\Statement\Select::orLike
     */
    public function testShouldThrowAnExceptionInOrLike()
    {
        //arrange
        $this->database->select('posts', ['id'])->orLike('title', '%TestLike');
    }

    /**
     * @group integration-database-select-like
     */
    public function testShouldReturnDataExpectedWithNotLike()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test',
            'visited' => 100,
            'category_id' => $lastInsertIdCat
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2',
            'visited' => 200,
            'category_id' => $lastInsertIdCat
        ];
        $this->insertRowAndReturnInsertResult('posts', $post2);
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $selectStatement = $this->database->select('posts', ['id'])
            ->notLike('title', '%Test 2');

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = [['id'=>$lastInsertIdPost]];
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-like
     * @expectedException \Tdw\RDB\Exception\StatementExecuteException
     * @expectedExceptionMessage The where clause should be called before Tdw\RDB\Statement\Select::orNotLike
     */
    public function testShouldThrowAnExceptionInOrNotLike()
    {
        //arrange
        $this->database->select('posts', ['id'])->orNotLike('title', '%Test 2');
    }

    /**
     * @group integration-database-select-null
     */
    public function testShouldReturnDataExpectedWithNull()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test',
            'visited' => 100,
            'category_id' => null
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2',
            'visited' => 200,
            'category_id' => $lastInsertIdCat
        ];
        $this->insertRowAndReturnInsertResult('posts', $post2);
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $selectStatement = $this->database->select('posts', ['id'])
            ->null('category_id');

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = [['id'=>$lastInsertIdPost]];
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-null
     * @expectedException \Tdw\RDB\Exception\StatementExecuteException
     * @expectedExceptionMessage The where clause should be called before Tdw\RDB\Statement\Select::orNull
     */
    public function testShouldThrowAnExceptionInOrNull()
    {
        //arrange
        $this->database->select('posts', ['id'])->orNull('category_id');
    }

    /**
     * @group integration-database-select-null
     */
    public function testShouldReturnDataExpectedWithNotNull()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test',
            'visited' => 100,
            'category_id' => $lastInsertIdCat
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2',
            'visited' => 200,
            'category_id' => null
        ];
        $this->insertRowAndReturnInsertResult('posts', $post2);
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $selectStatement = $this->database->select('posts', ['id'])
            ->notNull('category_id');

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = [['id'=>$lastInsertIdPost]];
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-null
     * @expectedException \Tdw\RDB\Exception\StatementExecuteException
     * @expectedExceptionMessage The where clause should be called before Tdw\RDB\Statement\Select::orNotNull
     */
    public function testShouldThrowAnExceptionInOrNotNull()
    {
        //arrange
        $this->database->select('posts', ['id'])->orNotNull('category_id');
    }

    /**
     * @group integration-database-select-order
     */
    public function testShouldReturnDataExpectedWithOrderByAsc()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test',
            'visited' => 100,
            'category_id' => $lastInsertIdCat
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2',
            'visited' => 200,
            'category_id' => $lastInsertIdCat
        ];
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $lastInsertIdPost2 = $this->insertRowAndReturnInsertResult('posts', $post2)->lastInsertId();
        $selectStatement = $this->database->select('posts', ['id'])
            ->orderBy('id', 'ASC');

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = [['id'=>$lastInsertIdPost], ['id'=>$lastInsertIdPost2]];
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-order
     */
    public function testShouldReturnDataExpectedWithOrderByDesc()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test',
            'visited' => 100,
            'category_id' => $lastInsertIdCat
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2',
            'visited' => 200,
            'category_id' => $lastInsertIdCat
        ];
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $lastInsertIdPost2 = $this->insertRowAndReturnInsertResult('posts', $post2)->lastInsertId();
        $selectStatement = $this->database->select('posts', ['id'])
            ->orderBy('id', 'DESC');

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = [['id'=>$lastInsertIdPost2], ['id'=>$lastInsertIdPost]];
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-order
     */
    public function testShouldReturnDataExpectedWithWhereAndOrderByDesc()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test',
            'visited' => 100,
            'category_id' => $lastInsertIdCat
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2',
            'visited' => 200,
            'category_id' => $lastInsertIdCat
        ];
        $post3 = [
            'title' => 'Title RDB Test 3',
            'description' => 'Description RDB Test 3',
            'visited' => 300,
            'category_id' => $lastInsertIdCat
        ];
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $lastInsertIdPost2 = $this->insertRowAndReturnInsertResult('posts', $post2)->lastInsertId();
        $lastInsertIdPost3 = $this->insertRowAndReturnInsertResult('posts', $post3)->lastInsertId();
        $selectStatement = $this->database->select('posts', ['id'])
            ->where('visited', '>', 100)
            ->orderBy('id', 'DESC');

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = [['id'=>$lastInsertIdPost3], ['id'=>$lastInsertIdPost2]];
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-limit
     */
    public function testShouldReturnDataExpectedWithLimit()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test',
            'visited' => 100,
            'category_id' => $lastInsertIdCat
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2',
            'visited' => 200,
            'category_id' => $lastInsertIdCat
        ];
        $post3 = [
            'title' => 'Title RDB Test 3',
            'description' => 'Description RDB Test 3',
            'visited' => 300,
            'category_id' => $lastInsertIdCat
        ];
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $lastInsertIdPost2 = $this->insertRowAndReturnInsertResult('posts', $post2)->lastInsertId();
        $lastInsertIdPost3 = $this->insertRowAndReturnInsertResult('posts', $post3)->lastInsertId();
        $selectStatement = $this->database->select('posts', ['id'])
            ->limit(2);

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = [['id'=>$lastInsertIdPost], ['id'=>$lastInsertIdPost2]];
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-limit
     */
    public function testShouldReturnDataExpectedWithLimitAndOffset()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test',
            'visited' => 100,
            'category_id' => $lastInsertIdCat
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2',
            'visited' => 200,
            'category_id' => $lastInsertIdCat
        ];
        $post3 = [
            'title' => 'Title RDB Test 3',
            'description' => 'Description RDB Test 3',
            'visited' => 300,
            'category_id' => $lastInsertIdCat
        ];
        $post4 = [
            'title' => 'Title RDB Test 4',
            'description' => 'Description RDB Test 4',
            'visited' => 400,
            'category_id' => $lastInsertIdCat
        ];
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $lastInsertIdPost2 = $this->insertRowAndReturnInsertResult('posts', $post2)->lastInsertId();
        $lastInsertIdPost3 = $this->insertRowAndReturnInsertResult('posts', $post3)->lastInsertId();
        $lastInsertIdPost4 = $this->insertRowAndReturnInsertResult('posts', $post4)->lastInsertId();
        $selectStatement = $this->database->select('posts', ['id'])
            ->limit(2, 2);

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = [['id'=>$lastInsertIdPost3], ['id'=>$lastInsertIdPost4]];
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-select-limit
     */
    public function testShouldReturnDataExpectedWithWhereAndLimit()
    {
        //arrange
        $category = [
            'name' => 'Category Name'
        ];
        $lastInsertIdCat = $this->insertRowAndReturnInsertResult('categories', $category)->lastInsertId();
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test',
            'visited' => 100,
            'category_id' => $lastInsertIdCat
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2',
            'visited' => 200,
            'category_id' => $lastInsertIdCat
        ];
        $post3 = [
            'title' => 'Title RDB Test 3',
            'description' => 'Description RDB Test 3',
            'visited' => 300,
            'category_id' => $lastInsertIdCat
        ];
        $post4 = [
            'title' => 'Title RDB Test 4',
            'description' => 'Description RDB Test 4',
            'visited' => 400,
            'category_id' => $lastInsertIdCat
        ];
        $lastInsertIdPost = $this->insertRowAndReturnInsertResult('posts', $post)->lastInsertId();
        $lastInsertIdPost2 = $this->insertRowAndReturnInsertResult('posts', $post2)->lastInsertId();
        $lastInsertIdPost3 = $this->insertRowAndReturnInsertResult('posts', $post3)->lastInsertId();
        $lastInsertIdPost4 = $this->insertRowAndReturnInsertResult('posts', $post4)->lastInsertId();
        $selectStatement = $this->database->select('posts', ['id'])
            ->where('visited', '<', 400)->limit(2);

        /**@var \Tdw\RDB\Result\Select $result */
        $result = $selectStatement->execute();

        //act
        $expected = [['id'=>$lastInsertIdPost], ['id'=>$lastInsertIdPost2]];
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @group integration-database-update
     */
    public function testShouldUpdatePostOnDatabase()
    {
        //arrange
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test'
        ];
        $post2 = [
            'title' => 'Title RDB Test 2',
            'description' => 'Description RDB Test 2'
        ];
        /**@var \Tdw\RDB\Result\Insert $resultInsert */
        $resultInsert = $this->insertRowAndReturnInsertResult('posts', $post);
        $lastInsertId = $resultInsert->lastInsertId();

        /**@var \Tdw\RDB\Result\Update $resultUpdate */
        $updateStatement = $this->database->update('posts', $post2, ['id'=>$lastInsertId]);
        $resultUpdate = $updateStatement->execute();

        //act
        $expected = 1;

        //assert
        $this->assertEquals($expected, $resultUpdate->rowCount());
    }

    /**
     * @group integration-database-delete
     */
    public function testShouldDeletePostOnDatabase()
    {
        //arrange
        $post = [
            'title' => 'Title RDB Test',
            'description' => 'Description RDB Test'
        ];
        /**@var \Tdw\RDB\Result\Insert $resultInsert */
        $resultInsert = $this->insertRowAndReturnInsertResult('posts', $post);
        $lastInsertId = $resultInsert->lastInsertId();

        /**@var \Tdw\RDB\Result\Delete $resultDelete */
        $deleteStatement = $this->database->delete('posts', ['id'=>$lastInsertId]);
        $resultDelete = $deleteStatement->execute();

        //act
        $expected = 1;

        //assert
        $this->assertEquals($expected, $resultDelete->rowCount());
    }

    private function insertRowAndReturnInsertResult(string $table, array $parameters): InsertResult
    {
        $insertStatement = $this->database->insert($table, $parameters);
        return $insertStatement->execute();
    }
}
