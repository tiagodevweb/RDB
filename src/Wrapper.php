<?php

declare(strict_types=1);

namespace Tdw\RDB;

use Tdw\RDB\Contract\Database as DatabaseInterface;

class Wrapper
{

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var array
     */
    private $default_options = [
        \PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8",
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
    ];

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function getDatabase(): DatabaseInterface
    {
        $db_driver = $this->getConfig('db_driver');
        $db_host = $this->getConfig('db_host');
        $db_port = $this->getConfig('db_port');
        $db_name = $this->getConfig('db_name');
        $db_user = $this->getConfig('db_user');
        $db_pass = $this->getConfig('db_pass');

        try {
            return new Database(new \PDO(
                "{$db_driver}:host={$db_host};dbname={$db_name};port={$db_port}",
                $db_user, $db_pass,
                isset($this->parameters['options']) &&
                is_array($this->parameters['options']) ?
                    array_merge($this->default_options, $this->parameters['options']) :
                    $this->default_options
            ));
        } catch (\PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * @param $key
     * @return string
     * @throws
     */
    private function getConfig($key): string
    {
        if (isset($this->parameters[$key])) {
            return $this->parameters[$key];
        }
        throw new \DomainException("Key {$key} not found");
    }

}
