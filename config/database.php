<?php
/**
 * Database Connection Class
 * Menggunakan PDO dengan SQLite
 * Singleton pattern untuk satu instance connection
 */

class Database {
    private static $instance = null;
    private $conn;
    private $dbPath;
    
    private function __construct() {
        // Path ke database SQLite
        $this->dbPath = __DIR__ . "/../database/laundry.db";
        
        try {
            // Pastikan folder database exists
            $dbDir = dirname($this->dbPath);
            if (!file_exists($dbDir)) {
                mkdir($dbDir, 0777, true);
            }
            
            // Create PDO connection
            $this->conn = new PDO("sqlite:" . $this->dbPath);
            
            // Set error mode to exceptions
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Enable foreign keys (SQLite memerlukan ini diaktifkan manual)
            $this->conn->exec("PRAGMA foreign_keys = ON;");
            
            // Set default fetch mode
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) {
            die("Database Connection Error: " . $e->getMessage());
        }
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
    
    /**
     * Get PDO connection object
     */
    public function getConnection() {
        return $this->conn;
    }
    
    /**
     * Prevent cloning of instance
     */
    private function __clone() {}
    
    /**
     * Prevent unserializing of instance
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
?>
