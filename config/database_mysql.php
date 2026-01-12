<?php

/**
 * MySQL Database Connection Class
 * Menggunakan PDO dengan MySQL
 * Singleton pattern untuk satu instance connection
 */

class Database
{
    private static $instance = null;
    private $conn;

    // Konfigurasi database MySQL
    private $host = "localhost";
    private $db_name = "laundry_dfour";
    private $username = "root";
    private $password = "";
    private $charset = "utf8mb4";

    private function __construct()
    {
        try {
            // DSN (Data Source Name) untuk MySQL
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";

            // Options untuk PDO
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset} COLLATE utf8mb4_unicode_ci"
            ];

            // Create PDO connection
            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }

    /**
     * Get singleton instance
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * Get PDO connection object
     */
    public function getConnection()
    {
        return $this->conn;
    }

    /**
     * Get database configuration
     */
    public function getConfig()
    {
        return [
            'host' => $this->host,
            'database' => $this->db_name,
            'username' => $this->username,
            'charset' => $this->charset
        ];
    }

    /**
     * Prevent cloning of instance
     */
    private function __clone() {}

    /**
     * Prevent unserializing of instance
     */
    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }
}
