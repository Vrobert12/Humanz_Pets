<?php
include "functions.php";
$autoload = new Functions();

$pdo = $autoload->connect($GLOBALS['dsn'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $GLOBALS['pdoOptions']);

// Function to delete files that are no longer referenced in the database
function deleteUnreferencedFiles($directory, $columnName, $tables) {
    global $pdo;

    // Initialize an array to hold all referenced files
    $referencedFiles = [];

    // Loop through each table and fetch the referenced files
    foreach ($tables as $table) {
        // Query to get all values of the column (profilePic or qrCodeName) in the given table
        $query = "SELECT $columnName FROM $table WHERE $columnName IS NOT NULL";
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Add the referenced files to the array
        foreach ($results as $result) {
            $referencedFiles[] = $result[$columnName];
        }
    }

    // Get all files in the directory
    $files = array_diff(scandir($directory), array('..', '.'));

    // Loop through all files in the directory and check if they are referenced in the database
    foreach ($files as $file) {
        $filePath = $directory . DIRECTORY_SEPARATOR . $file;

        // Check if the file is not referenced in any of the tables
        if (!in_array($file, $referencedFiles)) {
            if (file_exists($filePath)) {
                unlink($filePath); // Delete the file if not referenced
                echo "Deleted: $file\n";
            }
        }
    }
}

// Directories for profilePic and qrCodeName
$profilePicDirectory = 'pictures';
$qrCodeDirectory = 'QRcodes';

// Tables to check for referenced files
$tablesForProfilePic = ['user', 'veterinarian','pet'];
$tablesForQrCode = ['qr_code'];

// Delete unreferenced profile pictures from both tables (user and veterinarian)
deleteUnreferencedFiles($profilePicDirectory, 'profilePic', $tablesForProfilePic);

// Delete unreferenced QR code files from qr_code table (since QR codes are stored in QRcodes/ folder)
function deleteUnreferencedQRCodeFiles($directory, $columnName, $table) {
    global $pdo;

    // Query to get all qrCodeName values from the qr_code table
    $query = "SELECT $columnName FROM $table WHERE $columnName IS NOT NULL";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get all files in the directory
    $files = array_diff(scandir($directory), array('..', '.'));

    // Get all referenced QR code files from the database
    $referencedFiles = array_map(function ($file) {
        return basename($file); // Extract the file name without the directory path
    }, array_column($results, $columnName));

    // Loop through all files in the directory and check if they are referenced in the database
    foreach ($files as $file) {
        $filePath = $directory . DIRECTORY_SEPARATOR . $file;

        // Check if the file is not referenced in the database
        if (!in_array($file, $referencedFiles)) {
            if (file_exists($filePath)) {
                unlink($filePath); // Delete the file if not referenced
                echo "Deleted QR Code: $file\n";
            }
        }
    }
}

// Delete unreferenced QR code files
deleteUnreferencedQRCodeFiles($qrCodeDirectory, 'qrCodeName', 'qr_code');
?>
