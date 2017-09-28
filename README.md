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

    $pdo = new PDO('mysql:host=localhost;dbname=blog_test','root','root',[
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

## Tdw\Database

```php
->select(string $table, array $columns = ['*']): Tdw\Statement\Select
->insert(string $table, array $parameters): Tdw\Statement\Insert
->update(string $table, array $parameters, array $conditions): Tdw\Statement\Update
->delete(string $table, array $conditions): Tdw\Statement\Delete
->selectSQL(string $sql, array $parameters = []): Tdw\Statement\Select
->beginTransaction(): bool
->commit(): bool
->rollBack(): bool
```
 
## Statement Class

`Tdw\Statement\Select`<br />
`Tdw\Statement\Insert`<br />
`Tdw\Statement\Update`<br />
`Tdw\Statement\Delete`

### Statement Method

> Used only in Tdw\Statement\Select
```php
->columns(array $columns): Tdw\Statement\Select
->join(
  string $childTable,
  string $foreignKeyChild,
  string $operator,
  string $primaryKeyParent
): Tdw\Statement\Select
->where(string $column, string $operator, $value): Tdw\Statement\Select
->orWhere(string $column, string $operator, $value): Tdw\Statement\Select
->between(string $column, $valueOne, $valueTwo): Tdw\Statement\Select
->notBetween(string $column, $valueOne, $valueTwo): Tdw\Statement\Select
->orBetween(string $column, $valueOne, $valueTwo): Tdw\Statement\Select
->orNotBetween(string $column, $valueOne, $valueTwo): Tdw\Statement\Select
->in(string $column, array $subSet): Tdw\Statement\Select
->notIn(string $column, array $subSet): Tdw\Statement\Select
->orIn(string $column, array $subSet): Tdw\Statement\Select
->orNotIn(string $column, array $subSet): Tdw\Statement\Select
->like(string $column, string $value): Tdw\Statement\Select
->orLike(string $column, string $value): Tdw\Statement\Select
->notLike(string $column, string $value): Tdw\Statement\Select
->orNotLike(string $column, string $value): Tdw\Statement\Select
->null(string $column): Tdw\Statement\Select
->orNull(string $column): Tdw\Statement\Select
->notNull(string $column): Tdw\Statement\Select
->orNotNull(string $column): Tdw\Statement\Select
->orderBy(string $columns, $designator = 'ASC'): Tdw\Statement\Select
->limit(int $count, int $offset = 0): Tdw\Statement\Select
```

> Used all statement
```php
->execute()
->parameters(): array
->__toString()
```
 
## Result Class

`Tdw\Result\Select`<br />
`Tdw\Result\Insert`<br />
`Tdw\Result\Update`<br />
`Tdw\Result\Delete`

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