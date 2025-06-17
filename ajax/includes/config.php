<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

// Set timezone
date_default_timezone_set($_ENV['TIMEZONE']);

// Database connection parameters
$dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_NAME'] . ";charset=" . $_ENV['DB_CHARSET'];

$pdoOptions = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];


