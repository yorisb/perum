<?php
// Informasi koneksi database
$host = 'srv143.niagahoster.com';      // Host database (biasanya 'localhost')
$username = 'n1572337_developer';       // Username database
$password = 'bad_cookies8080';           // Password database
$database = 'n1572337_developer'; // Nama database yang digunakan

// Membuat koneksi ke database
$koneksi = mysqli_connect($host, $username, $password, $database);

// Cek koneksi
if (!$koneksi) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset ke utf8 untuk mendukung karakter khusus
mysqli_set_charset($koneksi, "utf8");

// Fungsi untuk membersihkan input
function bersihkanInput($data) {
    global $koneksi;
    // Menghilangkan spasi di awal dan akhir
    $data = trim($data);
    // Menghilangkan backslashes
    $data = stripslashes($data);
    // Mencegah SQL injection
    $data = mysqli_real_escape_string($koneksi, $data);
    return $data;
}

// Fungsi untuk menampilkan pesan alert
function tampilkanAlert($pesan, $tipe = 'success') {
    echo '<div class="alert alert-' . $tipe . ' alert-dismissible fade show" role="alert">
            ' . $pesan . '
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>';
}

// Fungsi untuk redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Fungsi untuk mendapatkan data dari tabel
function getData($table, $where = null, $order = null, $limit = null) {
    global $koneksi;
    
    $query = "SELECT * FROM $table";
    
    if ($where != null) {
        $query .= " WHERE $where";
    }
    
    if ($order != null) {
        $query .= " ORDER BY $order";
    }
    
    if ($limit != null) {
        $query .= " LIMIT $limit";
    }
    
    $result = mysqli_query($koneksi, $query);
    
    if (!$result) {
        die("Query error: " . mysqli_error($koneksi));
    }
    
    $data = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    return $data;
}

// Fungsi untuk mengeksekusi query
function executeQuery($query) {
    global $koneksi;
    $result = mysqli_query($koneksi, $query);
    
    if (!$result) {
        die("Query error: " . mysqli_error($koneksi));
    }
    
    return $result;
}