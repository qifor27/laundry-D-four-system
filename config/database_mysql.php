<?php

/**
 * MySQL Database Connection Class - Auto Environment Detection
 * Otomatis mendeteksi localhost (development) atau Hostinger (production)
 * Menggunakan PDO dengan MySQL
 */

class Database
{
    private static $instance = null;
    private $conn;
    private $environment;

    // ============================================================
    // KONFIGURASI DATABASE
    // ============================================================
    
    // Konfigurasi untuk localhost (development - XAMPP/Laragon)
    private $local = [
        'host'     => 'localhost',
        'db_name'  => 'laundry_dfour',
        'username' => 'root',
        'password' => '',
    ];

    // Konfigurasi untuk Hostinger (production)
    // ⚠️ GANTI DENGAN KREDENSIAL HOSTINGER ANDA
    private $production = [
        'host'     => 'localhost',
        'db_name'  => 'u983453370_laundry_dfour',    // Ganti dengan nama database Hostinger
        'username' => 'u983453370_kelompok2',       // Ganti dengan username Hostinger
        'password' => 'u983453370_Laundry_dfour',     // Ganti dengan password Hostinger
    ];

    private $charset = "utf8mb4";

    // ============================================================

    private function __construct()
    {
        // Deteksi environment
        $this->environment = $this->detectEnvironment();
        
        // Pilih konfigurasi berdasarkan environment
        $config = ($this->environment === 'production') ? $this->production : $this->local;

        try {
            $dsn = "mysql:host={$config['host']};dbname={$config['db_name']};charset={$this->charset}";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$this->charset} COLLATE utf8mb4_unicode_ci"
            ];

            $this->conn = new PDO($dsn, $config['username'], $config['password'], $options);
            
        } catch (PDOException $e) {
            // Log error
            error_log("[{$this->environment}] Database Connection Error: " . $e->getMessage());
            
            // Tampilkan error detail di localhost, pesan generic di production
            if ($this->environment === 'local') {
                die("Database Connection Error: " . $e->getMessage());
            } else {
                die("Maaf, terjadi kesalahan koneksi database. Silakan coba lagi nanti.");
            }
        }
    }

    /**
     * Deteksi apakah berjalan di localhost atau production
     */
    private function detectEnvironment()
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        
        // Daftar host yang dianggap sebagai localhost/development
        $localHosts = [
            'localhost',
            '127.0.0.1',
            '::1',
        ];

        // Cek apakah host adalah localhost
        foreach ($localHosts as $localHost) {
            if (stripos($host, $localHost) !== false) {
                return 'local';
            }
        }

        // Cek apakah ada port (biasanya development server)
        if (preg_match('/:\d+$/', $host)) {
            return 'local';
        }

        // Jika bukan localhost, anggap production
        return 'production';
    }

    /**
     * Get current environment
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Check if running in production
     */
    public function isProduction()
    {
        return $this->environment === 'production';
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
     * Get database configuration (tanpa password untuk keamanan)
     */
    public function getConfig()
    {
        $config = ($this->environment === 'production') ? $this->production : $this->local;
        
        return [
            'environment' => $this->environment,
            'host'        => $config['host'],
            'database'    => $config['db_name'],
            'username'    => $config['username'],
            'charset'     => $this->charset
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