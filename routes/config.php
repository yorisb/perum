<?php
$host = '';      // nama host MySQL
$username = '';      // username MySQL
$password = '';         // password MySQL
$database = '';      // nama database
$port = '';                        // port MySQL

// Membuat koneksi ke database menggunakan MySQLi
$conn = new mysqli($host, $username, $password, $database, $port);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
