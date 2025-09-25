<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");

/**
 * Database Schema class
 * 
 * * This class provides methods to create and manage database schemas.
 * * It includes methods to create tables and manage relationships.
 * * @package Schema
 * * @version 1.0
 * * @author Your Name
 * * @license MIT
 * * @link    
 * * @since   1.0
 * 
 */
class Schema {
    private $conn;

    /**
     * Class Constructor
     * 
     * @return void
     */
    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    /**
     * Create the users table
     * 
     * @return bool True on success, false on failure
     */
    public function createUsersTable() {
        $sql = "CREATE TABLE IF NOT EXISTS users (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL
        )";
        return $this->conn->query($sql);
    }

    /**
     * Create the posts table
     * 
     * @return bool True on success, false on failure
     */

    // FORMALLY createPostsTable
    public function createCarsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS car (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            cars_id INT(11) NOT NULL,
            cars_name VARCHAR(255) NOT NULL,
            price INT(11) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (cars_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        return $this->conn->query($sql);
    }

    /**
     * Add optional detail columns to car table (idempotent on MySQL 8+)
     * @return void
     */
    public function addCarDetailColumns() {
        $columns = [
            'brand' => 'VARCHAR(100) NULL',
            'model' => 'VARCHAR(100) NULL',
            'body_style' => 'VARCHAR(100) NULL',
            'car_condition' => 'VARCHAR(50) NULL',
            'fuel_type' => 'VARCHAR(50) NULL',
            'year' => 'INT(4) NULL'
        ];
        foreach ($columns as $name => $definition) {
            try {
                $stmt = $this->conn->prepare("SELECT COUNT(*) AS cnt FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'car' AND column_name = ?");
                $stmt->execute([$name]);
                $exists = (int)$stmt->fetchColumn() > 0;
                if (!$exists) {
                    $this->conn->query("ALTER TABLE car ADD COLUMN `{$name}` {$definition}");
                }
            } catch (Exception $e) {
                // ignore to avoid breaking app on shared hosting/MySQL variants
            }
        }
    }

    /**
     * Create the comments table
     * 
     * @return bool True on success, false on failure
     */
    public function createTestimonialsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS testimonials (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            Testimonials_id INT(11) NOT NULL,
            cars_id INT(11) NOT NULL,
            content TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (cars_id) REFERENCES car(id) ON DELETE CASCADE,
            FOREIGN KEY (Testimonials_id) REFERENCES users(id) ON DELETE CASCADE

        )";
        return $this->conn->query($sql);    
    }

    /**
     * Create the likes table
     * 
     * @return bool True on success, false on failure
     */
    public function createLikesTable() {
        $sql = "CREATE TABLE IF NOT EXISTS likes (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            post_id INT(11) NOT NULL,
            cars_id INT(11) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (cars_id) REFERENCES car(id) ON DELETE CASCADE,
            FOREIGN KEY (post_id) REFERENCES testimonials(id) ON DELETE CASCADE

        )";
        return $this->conn->query($sql);
    }

    /**
     * Session table creation
     * * @return bool True on success, false on failure
     */
    public function createSessionsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS sessions (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            cars_id INT(11) NULL,
            session_id VARCHAR(255) NOT NULL,
            session_start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            session_end_time TIMESTAMP NULL DEFAULT NULL,
            session_token VARCHAR(255) NOT NULL,
            session_status ENUM('active', 'inactive') DEFAULT 'active',
            UNIQUE (session_id),
            UNIQUE (session_token),
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT NOT NULL,
            FOREIGN KEY (cars_id) REFERENCES users(id) ON DELETE CASCADE
        )";
        return $this->conn->query($sql);
    }

    /**
     * Purchases table for buying cars
     */
    public function createPurchasesTable() {
        $sql = "CREATE TABLE IF NOT EXISTS purchases (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            cars_id INT(11) NOT NULL,
            price INT(11) NOT NULL,
            status ENUM('pending','completed','cancelled') DEFAULT 'completed',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (car_id) REFERENCES car(id) ON DELETE CASCADE
        )";
        return $this->conn->query($sql);
    }

    /**
     * Rentals table for renting cars
     */
    public function createRentalsTable() {
        $sql = "CREATE TABLE IF NOT EXISTS rentals (
            id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_id INT(11) NOT NULL,
            car_id INT(11) NOT NULL,
            start_date DATE NOT NULL,
            end_date DATE NOT NULL,
            daily_rate INT(11) NULL,
            status ENUM('pending','active','completed','cancelled') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (car_id) REFERENCES car(id) ON DELETE CASCADE
        )";
        return $this->conn->query($sql);
    }
}