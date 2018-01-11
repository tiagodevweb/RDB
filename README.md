# RDB - Relational Database

Simple relational database layer that abstracts SQL script to more usual persistence functions. 
It still provides a selectSQL function for more advanced and customized queries.

[![Build Status](https://travis-ci.org/tiagodevweb/rdb.svg?branch=master)](https://travis-ci.org/tiagodevweb/rdb)

## Requirements

PHP: >=7.0

## Install

```bash
$ composer require tdw/rdb
```

## Usage

#### require autoload

```php
<?php

$config = [
    'db_driver' => 'mysql',
    'db_host' => 'localhost',
    'db_port' => '3306',
    'db_name' => 'blog',
    'db_user' => 'root',
    'db_pass' => 'root'
];

$rdb = new \Tdw\RDB\Wrapper($config);
$database = $rdb->getDatabase();
```

```php
<?php

try {    
    //select
    $select = $database->select('posts');
    /**@var \Tdw\RDB\Result\Select $result*/
    $result = $select->execute();
    print_r($result->fetchAll());    
} catch (Tdw\RDB\Exception\StatementExecuteException $e) {
    die($e->getPrevious());
}
```

```php
<?php

try {    
    //insert
    $insert = $database->insert(
        'posts',
        ['title'=>'Post title','description'=>'Post desc']
    );
    /**@var \Tdw\RDB\Result\Insert $result*/
    $result = $insert->execute();
    print_r($result->lastInsertId());    
} catch (Tdw\RDB\Exception\StatementExecuteException $e) {
    die($e->getPrevious());
}
```

```php
<?php

try {    
    //update
    $update = $database->update(
        'posts',
        ['title'=>'Post title','description'=>'Post desc'],
        ['id' => 1]
    );
    /**@var \Tdw\RDB\Result\Update $result*/
    $result = $update->execute();
    print_r($result->rowCount());    
} catch (Tdw\RDB\Exception\StatementExecuteException $e) {
    die($e->getPrevious());
}
```

```php
<?php

try {    
    //delete
    $delete = $database->delete('posts', ['id' => 1]);
    /**@var \Tdw\RDB\Result\Delete $result*/
    $result = $delete->execute();
    print_r($result->rowCount());    
} catch (Tdw\RDB\Exception\StatementExecuteException $e) {
    die($e->getPrevious());
}
```

## Tdw\RDB\Database

```php
->select(string $table, array $columns = ['*'])
->insert(string $table, array $parameters)
->update(string $table, array $parameters, array $conditions)
->delete(string $table, array $conditions)
->selectSQL(string $sql, array $parameters = [])
->beginTransaction()
->commit()
->rollBack()
```
 
## Statement Class

`Tdw\RDB\Statement\Select`<br />
`Tdw\RDB\Statement\Insert`<br />
`Tdw\RDB\Statement\Update`<br />
`Tdw\RDB\Statement\Delete`

### Statement Method

> Used only in Tdw\RDB\Statement\Select
```php
->columns(array $columns)
->join(
  string $childTable,
  string $foreignKeyChild,
  string $operator,
  string $primaryKeyParent
)
->where(string $column, string $operator, $value)
->orWhere(string $column, string $operator, $value)
->between(string $column, $valueOne, $valueTwo)
->notBetween(string $column, $valueOne, $valueTwo)
->orBetween(string $column, $valueOne, $valueTwo)
->orNotBetween(string $column, $valueOne, $valueTwo)
->in(string $column, array $subSet)
->notIn(string $column, array $subSet)
->orIn(string $column, array $subSet)
->orNotIn(string $column, array $subSet)
->like(string $column, string $value)
->orLike(string $column, string $value)
->notLike(string $column, string $value)
->orNotLike(string $column, string $value)
->null(string $column)
->orNull(string $column)
->notNull(string $column)
->orNotNull(string $column)
->orderBy(string $columns, $designator = 'ASC')
->limit(int $count, int $offset = 0)
```

> Used all statement
```php
->execute()
->parameters(): array
->__toString()
```
 
## Result Class

`Tdw\RDB\Result\Select`<br />
`Tdw\RDB\Result\Insert`<br />
`Tdw\RDB\Result\Update`<br />
`Tdw\RDB\Result\Delete`

### Result Method

> Used only in Tdw\Result\Select
```php
->fetchAll(): array
->fetch(): array
```

> Used only in Tdw\Result\Insert
```php
->lastInsertId(string $name = null)
```

> Used all result
```php
->rowCount()
```