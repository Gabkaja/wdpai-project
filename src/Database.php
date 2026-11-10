<?php

declare(strict_types=1);

namespace App;

use PDO;

final class Database
{
    private string $host;
    private string $port;
    private string $dbname;
    private string $user;
    private string $password;

    public function __construct()
    {
        $this->host = getenv('DB_HOST');
        $this->port = getenv('DB_PORT');
        $this->dbname = getenv('DB_NAME');
        $this->user = getenv('DB_USER');
        $this->password = getenv('DB_PASSWORD');

        if ($this->host === false || $this->host === ''
            || $this->port === false || $this->port === ''
            || $this->dbname === false || $this->dbname === ''
            || $this->user === false || $this->user === ''
            || $this->password === false || $this->password === '') {
            throw new \RuntimeException('Database configuration is incomplete.');
        }
    }

    public function connect(): PDO
    {
        $dsn = sprintf('pgsql:host=%s;port=%d;dbname=%s', $this->host, (int) $this->port, $this->dbname);

        try {
            return new PDO(
                $dsn,
                $this->user,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (\PDOException $exception) {
            error_log($exception->getMessage());

            throw new \RuntimeException('Could not connect to the database.', 0, $exception);
        }
    }
}
