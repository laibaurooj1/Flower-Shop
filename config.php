<?php
/**
 * Database connection.
 * Update these four values to match your local MySQL / MariaDB setup
 * (XAMPP / WAMP / MAMP default values are shown below).
 */
$DB_HOST = 'localhost';
$DB_USER = 'root';
$DB_PASS = '';
$DB_NAME = 'shop_db';

mysqli_report(MYSQLI_REPORT_OFF); // we handle errors ourselves below

$conn = mysqli_connect($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if (!$conn) {
    // Friendly message instead of leaking connection details / dying with die()
    http_response_code(500);
    die('Could not connect to the database. Please make sure MySQL is running and that '
        . 'shop_db.sql has been imported. (Check the credentials in config.php.)');
}

mysqli_set_charset($conn, 'utf8mb4');
