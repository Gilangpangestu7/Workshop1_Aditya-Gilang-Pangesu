<?php
// Konfigurasi database
$host = "localhost";
$user = "root";
$pass = "";
$db   = "hospital";

// Koneksi ke database
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
