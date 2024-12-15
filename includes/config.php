<?php
date_default_timezone_set('Europe/Belgrade');

const PARAMS = [
    "HOST" => 'localhost',
    "USER" => 'root',
    "PASSWORD" => '',
    "DB" => 'pets',
    "CHARSET" => 'utf8mb4'
];

$dsn = "mysql:host=" . PARAMS['HOST'] . ";dbname=" . PARAMS['DB'] . ";charset=" . PARAMS['CHARSET'];

$pdoOptions = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false
];


