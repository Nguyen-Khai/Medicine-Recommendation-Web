<?php
$host = "localhost";
$dbname = "medicine_system";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
} catch (PDOException $e) {
    die("Kết nối thất bại: " . $e->getMessage());
}
