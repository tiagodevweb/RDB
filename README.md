# RDB - Relational Database

Simple relational database layer that abstracts SQL script to more usual persistence functions. 
It still provides a selectSQL function for more advanced and customized queries.

[![Build Status](https://travis-ci.org/tiagodevweb/rdb.svg?branch=master)](https://travis-ci.org/tiagodevweb/rdb)

## Requirements

PHP: >=7.1

## Install

```bash
$ composer require tdw/rdb
```

## Usage

#### `base`

```php
<?php

require 'vendor/autoload.php';
try {

    $pdo = new \PDO('mysql:host=localhost;dbname=blog_test','root','root',[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $database = new \Tdw\RDB\Database($pdo);
    
    //select
    $select = $database->select('posts');
    /**@var \Tdw\RDB\Result\Select $result*/
    //$result = $select->execute();
    //print_r($result->fetchAll());
    
    //insert
    $insert = $database->insert(
        'posts',
        ['title'=>'Post title','description'=>'Post desc']
    );
    /**@var \Tdw\RDB\Result\Insert $result*/
    //$result = $insert->execute();
    //print_r($result->lastInsertId());

    //update
    $update = $database->update(
        'posts',
        ['title'=>'Post title','description'=>'Post desc'],
        ['id' => 1]
    );
    /**@var \Tdw\RDB\Result\Update $result*/
    //$result = $update->execute();
    //print_r($result->rowCount());

    //delete
    $delete = $database->delete('posts', ['id' => 1]);
    /**@var \Tdw\RDB\Result\Delete $result*/
    //$result = $delete->execute();
    //print_r($result->rowCount());
    
} catch (\Exception $e){
    die($e->getPrevious());
}
```

## Tdw\RDB\Database

```php
->select(string $table, array $columns = ['*']): Tdw\RDB\Statement\Select
->insert(string $table, array $parameters): Tdw\RDB\Statement\Insert
->update(string $table, array $parameters, array $conditions): Tdw\RDB\Statement\Update
->delete(string $table, array $conditions): Tdw\RDB\Statement\Delete
->selectSQL(string $sql, array $parameters = []): Tdw\RDB\Statement\Select
->beginTransaction(): bool
->commit(): bool
->rollBack(): bool
```
 
## Statement Class

`Tdw\RDB\Statement\Select`<br />
`Tdw\RDB\Statement\Insert`<br />
`Tdw\RDB\Statement\Update`<br />
`Tdw\RDB\Statement\Delete`

### Statement Method

> Used only in Tdw\RDB\Statement\Select
```php
->columns(array $columns): Tdw\RDB\Statement\Select
->join(
  string $childTable,
  string $foreignKeyChild,
  string $operator,
  string $primaryKeyParent
): Tdw\RDB\Statement\Select
->where(string $column, string $operator, $value): Tdw\RDB\Statement\Select
->orWhere(string $column, string $operator, $value): Tdw\RDB\Statement\Select
->between(string $column, $valueOne, $valueTwo): Tdw\RDB\Statement\Select
->notBetween(string $column, $valueOne, $valueTwo): Tdw\RDB\Statement\Select
->orBetween(string $column, $valueOne, $valueTwo): Tdw\RDB\Statement\Select
->orNotBetween(string $column, $valueOne, $valueTwo): Tdw\RDB\Statement\Select
->in(string $column, array $subSet): Tdw\RDB\Statement\Select
->notIn(string $column, array $subSet): Tdw\RDB\Statement\Select
->orIn(string $column, array $subSet): Tdw\RDB\Statement\Select
->orNotIn(string $column, array $subSet): Tdw\RDB\Statement\Select
->like(string $column, string $value): Tdw\RDB\Statement\Select
->orLike(string $column, string $value): Tdw\RDB\Statement\Select
->notLike(string $column, string $value): Tdw\RDB\Statement\Select
->orNotLike(string $column, string $value): Tdw\RDB\Statement\Select
->null(string $column): Tdw\RDB\Statement\Select
->orNull(string $column): Tdw\RDB\Statement\Select
->notNull(string $column): Tdw\RDB\Statement\Select
->orNotNull(string $column): Tdw\RDB\Statement\Select
->orderBy(string $columns, $designator = 'ASC'): Tdw\RDB\Statement\Select
->limit(int $count, int $offset = 0): Tdw\RDB\Statement\Select
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
->fetchAll(int $style = self::TO_ARRAY): array
->fetch(int $style = self::TO_ARRAY)
```

> Used only in Tdw\Result\Insert
```php
->lastInsertId(string $name = null): int
```

> Used all result
```php
->rowCount(): int
```