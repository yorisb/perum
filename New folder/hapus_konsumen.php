<?php
$conn = new mysqli("srv143.niagahoster.com", "n1572337_developer", "bad_cookies8080", "n1572337_developer");

$kode = $_GET['kode'];
$conn->query("DELETE FROM calon_konsumen WHERE kode = '$kode'");

header("Location: daftar_konsumen.php");
exit;
