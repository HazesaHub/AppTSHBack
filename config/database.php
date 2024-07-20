<?php

class Database
{
    private $host = DB_SERVERNAME;
    private $db_name = DB_NAME;
    private $username = DB_USERNAME;
    private $password = DB_PASSWORD;
    protected $conn;

    public function __construct()
    {
        $this->conn = $this->getConnection();
    }

    protected function getConnection()
    {
        $this->conn = null;
        $connectionInfo = array(
            "Database" => $this->db_name,
            "UID" => $this->username,
            "PWD" => $this->password,
        );

        // conexcion con PDO
        try {
            $this->conn = new PDO("sqlsrv:Server=" . $this->host . ";Database=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $this->conn;
        } catch (PDOException $e) {
            return false;
        }
    }

    protected function createInsert(string $sql, array $params): string
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $this->conn->lastInsertId();
        } catch (PDOException $e) {
            return $stmt->errorInfo()[2];
        }
    }

    protected function getLastID(): string
    {
        return $this->conn->lastInsertId();
    }

    protected function Select(string $sql, array $params): array|string
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return $stmt->errorInfo()[2];
        }
    }

    protected function Update(string $sql, array $params): string
    {
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            return $stmt->errorInfo()[2];
        }
    }

}
