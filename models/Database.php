<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        $servername = "localhost";
        $username = "emotion_user";
        $password = "IPSSI2024";
        $database = "e_motion";

        $dsn = "mysql:host=$servername;dbname=$database;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $username, $password, $options);
        } catch (PDOException $e) {
            throw new PDOException("Connection failed: " . $e->getMessage(), (int)$e->getCode());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->pdo;
    }

    public static function isConnected() {
        return self::$instance !== null && self::$instance->pdo instanceof PDO;
    }
}
