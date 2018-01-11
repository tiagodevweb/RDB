<?php

namespace Tests\RDB;

use PHPUnit\Framework\TestCase;
use Tdw\RDB\Collection;
use Tdw\RDB\Database;
use Tdw\RDB\Exception\StatementExecuteException;
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
        $this->database = new Database(self::$pdo);
        $this->database->beginTransaction();
    }

    public function tearDown()
    {
        $this->database->rollBack();
        $this->database = null;
    }

    /**
     * @test
     */
    public function should_insert_row_in_database()
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
     * @test
     */
    public function should_return_data_expected()
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
        $expected = new Collection([$post,$post2]);
        $actual = $result->fetchAll();
        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_return_data_expected_with_where()
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
     * @test
     */
    public function should_throw_an_exception_in_or_where()
    {
        $this->expectException(StatementExecuteException::class);
        $this->expectExceptionMessage('The where clause should be called before Tdw\RDB\Statement\Select::orWhere');
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
     * @test
     */
    public function should_return_data_expected_with_where_or_where()
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
        $expected = new Collection([$post2, $post]);
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_return_data_expected_with_join()
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
     * @test
     */
    public function should_return_data_expected_with_join_and_alias()
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
     * @test
     */
    public function should_return_data_expected_with_between()
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
        $expected = new Collection([['id'=>$lastInsertIdPost]]);
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_throw_an_exception_in_or_between()
    {
        $this->expectException(StatementExecuteException::class);
        $this->expectExceptionMessage('The where clause should be called before Tdw\RDB\Statement\Select::orBetween');
        $this->database->select('posts', ['id'])->orBetween('visited', 50, 150);
    }

    /**
     * @test
     */
    public function should_return_data_expected_with_not_between()
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
        $expected = new Collection([['id'=>$lastInsertIdPost]]);
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_throw_an_exception_in_or_not_between()
    {
        $this->expectException(StatementExecuteException::class);
        $this->expectExceptionMessage('The where clause should be called before Tdw\RDB\Statement\Select::orNotBetween');
        $this->database->select('posts', ['id'])->orNotBetween('visited', 150, 250);
    }

    /**
     * @test
     */
    public function should_return_data_expected_with_in()
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
        $expected = new Collection([['id'=>$lastInsertIdPost]]);
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_throw_an_exception_in_or_in()
    {
        $this->expectException(StatementExecuteException::class);
        $this->expectExceptionMessage('The where clause should be called before Tdw\RDB\Statement\Select::orIn');
        $this->database->select('posts', ['id'])->orIn('visited', [100,101,102]);
    }

    /**
     * @test
     */
    public function should_return_data_expected_with_not_in()
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
        $expected = new Collection([['id'=>$lastInsertIdPost]]);
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_throw_an_exception_in_or_not_in()
    {
        $this->expectException(StatementExecuteException::class);
        $this->expectExceptionMessage('The where clause should be called before Tdw\RDB\Statement\Select::orNotIn');
        $this->database->select('posts', ['id'])->orNotIn('visited', [200,201,202]);
    }

    /**
     * @test
     */
    public function should_return_data_expected_with_like()
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
        $expected = new Collection([['id'=>$lastInsertIdPost]]);
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_throw_an_exception_in_or_like()
    {
        $this->expectException(StatementExecuteException::class);
        $this->expectExceptionMessage('The where clause should be called before Tdw\RDB\Statement\Select::orLike');
        $this->database->select('posts', ['id'])->orLike('title', '%TestLike');
    }

    /**
     * @test
     */
    public function should_return_data_expected_with_not_like()
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
        $expected = new Collection([['id'=>$lastInsertIdPost]]);
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_throw_an_exception_in_or_not_like()
    {
        $this->expectException(StatementExecuteException::class);
        $this->expectExceptionMessage('The where clause should be called before Tdw\RDB\Statement\Select::orNotLike');
        $this->database->select('posts', ['id'])->orNotLike('title', '%Test 2');
    }

    /**
     * @test
     */
    public function should_return_data_expected_with_null()
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
        $expected = new Collection([['id'=>$lastInsertIdPost]]);
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_throw_an_exception_in_or_null()
    {
        $this->expectException(StatementExecuteException::class);
        $this->expectExceptionMessage('The where clause should be called before Tdw\RDB\Statement\Select::orNull');
        $this->database->select('posts', ['id'])->orNull('category_id');
    }

    /**
     * @test
     */
    public function should_return_data_expected_with_not_null()
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
        $expected = new Collection([['id'=>$lastInsertIdPost]]);
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_throw_an_exception_in_or_not_null()
    {
        $this->expectException(StatementExecuteException::class);
        $this->expectExceptionMessage('The where clause should be called before Tdw\RDB\Statement\Select::orNotNull');
        $this->database->select('posts', ['id'])->orNotNull('category_id');
    }

    /**
     * @test
     */
    public function should_return_data_expected_with_order_by_asc()
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
        $expected = new Collection([['id'=>$lastInsertIdPost], ['id'=>$lastInsertIdPost2]]);
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_return_data_expected_with_order_by_desc()
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
        $expected = new Collection([['id'=>$lastInsertIdPost2], ['id'=>$lastInsertIdPost]]);
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_return_data_expected_with_where_and_order_by_desc()
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
        $expected = new Collection([['id'=>$lastInsertIdPost3], ['id'=>$lastInsertIdPost2]]);
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_return_data_expected_with_limit()
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
        $expected = new Collection([['id'=>$lastInsertIdPost], ['id'=>$lastInsertIdPost2]]);
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_return_data_expected_with_limit_and_offset()
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
        $expected = new Collection([['id'=>$lastInsertIdPost3], ['id'=>$lastInsertIdPost4]]);
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_return_data_expected_with_where_and_limit()
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
        $expected = new Collection([['id'=>$lastInsertIdPost], ['id'=>$lastInsertIdPost2]]);
        $actual = $result->fetchAll();

        //assert
        $this->assertEquals($expected, $actual);
    }

    /**
     * @test
     */
    public function should_update_post_on_database()
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
     * @test
     */
    public function should_delete_post_on_database()
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

    /**
     * @param string $table
     * @param array $parameters
     * @return InsertResult
     */
    private function insertRowAndReturnInsertResult(string $table, array $parameters): InsertResult
    {
        $insertStatement = $this->database->insert($table, $parameters);
        return $insertStatement->execute();
    }
}
