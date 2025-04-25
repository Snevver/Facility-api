<?php

namespace App\Plugins\Db;

use App\Plugins\Db\Connection\IConnection;
use PDO;
use PDOException;
use PDOStatement;

class Db implements IDb {
    /** @var PDO|null */
    private $connection = null;
    /** @var PDOStatement */
    private $stmt;

    /**
     * Constructor of this class
     * @param IConnection $connectionImplementation
     */
    public function __construct(IConnection $connectionImplementation) {
        $this->connection = $this->connect($connectionImplementation);
    }

    /**
     * Function to start a transaction
     */
    public function beginTransaction() {
        return $this->connection->beginTransaction();
    }

    /**
     * Function to roll back the transaction
     */
    public function rollBack() {
        return $this->connection->rollBack();
    }

    /**
     * Function to commit a transaction
     */
    public function commit() {
        return $this->connection->commit();
    }

    /**
     * @param string $query
     * @param array $bind
     * @return bool
     */
    public function executeQuery(string $query, array $bind = []): bool {
        $this->stmt = $this->connection->prepare($query);
        if ($bind) {
            return $this->stmt->execute($bind);
        }
        return $this->stmt->execute();
    }

    /**
     * Function to get last inserted id
     * @param mixed $name
     * @return int
     */
    public function getLastInsertedId($name = null): int {
        $id = $this->connection->lastInsertId($name);
        return ($id ?: false);
    }

    /**
     * Function to get the connection
     * @return null|PDO
     */
    public function getConnection(): ?PDO {
        return $this->connection;
    }

    /**
     * Function to connect the db.
     * @return PDO
     * @throws PDOException
     */
    private function connect(IConnection $connectionImplementation) {
        try {
            $dsn = $connectionImplementation->getDsn();
            // If you need to specify the port, do it correctly:
            $dsn .= ';port=3307';
            
            return new PDO(
                $dsn,
                $connectionImplementation->getUsername(),
                $connectionImplementation->getPassword(),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]
            );
        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Function to return the last executed statement if any
     * @return null|PDOStatement
     */
    public function getStatement(): ?PDOStatement {
        return $this->stmt;
    }
}
