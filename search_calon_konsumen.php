<?php
$conn = new mysqli("srv143.niagahoster.com", "n1572337_developer", "bad_cookies8080", "n1572337_developer");

$query = $_GET['query'];
$result = $conn->query("SELECT id, kode, nama_lengkap, no_hp FROM calon_konsumen 
                       WHERE nama_lengkap LIKE '%$query%' OR kode LIKE '%$query%' 
                       LIMIT 10");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="search-result-item calon-konsumen-item p-4 border-b border-gray-200 hover:bg-gray-100 cursor-pointer" 
                data-id="'.htmlspecialchars($row['id']).'" 
                data-kode="'.htmlspecialchars($row['kode']).'" 
                data-nama="'.htmlspecialchars($row['nama_lengkap']).'"
                data-telp="'.htmlspecialchars($row['no_hp']).'">
                <div class="font-semibold text-gray-800">'.$row['nama_lengkap'].'</div>
                <div class="text-sm text-gray-500">Kode: '.$row['kode'].'</div>
                <div class="text-sm text-gray-500">Telp: '.$row['no_hp'].'</div>
              </div>';
    }
} else {
    echo '<div class="search-result-item">Tidak ditemukan</div>';
}
?>