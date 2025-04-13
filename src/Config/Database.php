<?php

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $host = getenv('DB_HOST') ?: 'mysql';
        $dbname = getenv('DB_NAME') ?: 'empresa';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') ?: 'root';

        try {
            $this->connection = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $user,
                $pass,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]
            );
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }
} 