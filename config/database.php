<?php

class Database
{
    private $host;
    private $db_name;
    private $username;
    private $password ;
    protected $conn;

    public function __construct()
    {
        $this->host = DB_SERVERNAME;
        $this->db_name = DB_NAME;
        $this->username = DB_USERNAME;
        $this->password = DB_PASSWORD;
        $this->conn = $this->getConnection();
    }

    protected function getConnection()
    {
        $this->conn = null;
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
