<?php
$conn = new mysqli("srv143.niagahoster.com", "n1572337_developer", "bad_cookies8080", "n1572337_developer");

$query = $_GET['query'];
$result = $conn->query("SELECT id, kode, nama_lengkap, no_hp FROM calon_konsumen 
                       WHERE nama_lengkap LIKE '%$query%' OR kode LIKE '%$query%' 
                       LIMIT 10");

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo '<div class="search-result-item calon-konsumen-item" 
                data-id="'.$row['id'].'" 
                data-kode="'.$row['kode'].'" 
                data-nama="'.$row['nama_lengkap'].'"
                data-telp="'.$row['no_hp'].'">
                <div>'.$row['nama_lengkap'].'</div>
                <div class="customer-code">Kode: '.$row['kode'].'</div>
                <div class="customer-phone">Telp: '.$row['no_hp'].'</div>
              </div>';
    }
} else {
    echo '<div class="search-result-item">Tidak ditemukan</div>';
}
?>