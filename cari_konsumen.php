<?php
$conn = new mysqli("srv143.niagahoster.com", "n1572337_developer", "bad_cookies8080", "n1572337_developer");
$search = $_GET['search'] ?? '';

$query = mysqli_query($conn, "SELECT id, kode, nama_lengkap FROM calon_konsumen WHERE nama_lengkap LIKE '%$search%' LIMIT 20");
$data = [];

while ($row = mysqli_fetch_assoc($query)) {
  $data[] = $row;
}

echo json_encode($data);
