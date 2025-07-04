<?php
$host = 'srv143.niagahoster.com';      // nama host MySQL
$username = 'n1572337_developer';      // username MySQL
$password = 'bad_cookies8080';         // password MySQL
$database = 'n1572337_developer';      // nama database
$port = '3306';                        // port MySQL

// Membuat koneksi ke database menggunakan MySQLi
$conn = new mysqli($host, $username, $password, $database, $port);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
