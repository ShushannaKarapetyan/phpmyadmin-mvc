<?php

namespace app\core;

use PDO;

class Database
{
    /**
     * @var PDO
     */
    public PDO $pdo;

    /**
     * Database constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $dsn = $config['dsn'];
        $user = $config['user'];
        $password = $config['password'];

        $this->pdo = new PDO($dsn, $user, $password);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function up()
    {

    }

    public function createMigrationsTable()
    {
        //$this->pdo->exec()
    }
}