<?php
$host = "localhost";
$port = "3307";   // MySQL PORT
$db   = "mp3_muzik_sitesi";
$user = "root";
$pass = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
} catch (PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
