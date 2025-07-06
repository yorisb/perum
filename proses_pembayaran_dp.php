<?php
session_start();
require_once 'routes/config.php';

// Cek apakah request POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: daftar_transaksi.php');
    exit();
}

// Validasi input
$id_transaksi = isset($_POST['id_transaksi']) ? intval($_POST['id_transaksi']) : 0;
$no_angsuran = isset($_POST['no_angsuran']) ? intval($_POST['no_angsuran']) : 0;
$jumlah_bayar = isset($_POST['jumlah_bayar']) ? str_replace('.', '', $_POST['jumlah_bayar']) : 0;
$tgl_bayar = isset($_POST['tgl_bayar']) ? $_POST['tgl_bayar'] : date('Y-m-d');
$metode_pembayaran = isset($_POST['metode_pembayaran']) ? $_POST['metode_pembayaran'] : 'Tunai';
$keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : '';

// Validasi data
if ($id_transaksi <= 0 || $no_angsuran <= 0 || $jumlah_bayar <= 0) {
    $_SESSION['error_message'] = 'Data pembayaran tidak valid';
    header("Location: detail_transaksi.php?id=$id_transaksi");
    exit();
}

try {
    // Mulai transaksi
    $conn->begin_transaction();
    
    // 1. Simpan data pembayaran ke tabel transaksi_dp
    $stmt = $conn->prepare("
        INSERT INTO transaksi_dp (
            id_transaksi, 
            no_angsuran, 
            jumlah_bayar, 
            tgl_jatuh_tempo, 
            tgl_bayar, 
            status, 
            metode_pembayaran, 
            keterangan
        ) VALUES (?, ?, ?, ?, ?, 'Lunas', ?, ?)
        ON DUPLICATE KEY UPDATE
            jumlah_bayar = VALUES(jumlah_bayar),
            tgl_bayar = VALUES(tgl_bayar),
            status = VALUES(status),
            metode_pembayaran = VALUES(metode_pembayaran),
            keterangan = VALUES(keterangan),
            updated_at = NOW()
    ");
    
    // Untuk tgl_jatuh_tempo, kita perlu mengambil dari transaksi utama
    // Asumsi: jatuh tempo adalah 1 bulan setelah tanggal transaksi untuk angsuran pertama, dst
    $tgl_jatuh_tempo = date('Y-m-d', strtotime("+$no_angsuran month", strtotime($transaksi['tgl_transaksi'])));
    
    $stmt->bind_param("iidssss", 
        $id_transaksi, 
        $no_angsuran, 
        $jumlah_bayar, 
        $tgl_jatuh_tempo, 
        $tgl_bayar, 
        $metode_pembayaran, 
        $keterangan
    );
    $stmt->execute();
    
    // 2. Update total pembayaran di tabel transaksi utama jika diperlukan
    // (Anda bisa menambahkan logika ini sesuai kebutuhan sistem)
    
    // Commit transaksi
    $conn->commit();
    
    $_SESSION['success_message'] = "Pembayaran angsuran ke-$no_angsuran berhasil dicatat";
    header("Location: detail_transaksi.php?id=$id_transaksi");
    exit();
    
} catch (Exception $e) {
    // Rollback transaksi jika terjadi error
    $conn->rollback();
    
    $_SESSION['error_message'] = "Terjadi kesalahan: " . $e->getMessage();
    header("Location: detail_transaksi.php?id=$id_transaksi");
    exit();
}