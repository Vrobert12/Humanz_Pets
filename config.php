<?php
date_default_timezone_set("Europe/Belgrade");
session_start();
const PARAMS=[
    'HOST'=>'localhost',
    'USER'=>'root',
    'PASSWORD'=>'',

    'DATABASE'=>'pets',
    'CHARSET'=>'utf8mb4'
];
$dsn="mysql:host=".PARAMS['HOST'].";dbname=".PARAMS['DATABASE'].";charset=".PARAMS['CHARSET'];

$pdoOptions=[
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES=>false
];