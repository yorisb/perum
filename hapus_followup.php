<?php
session_start();
require_once 'koneksi.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Ambil ID follow up
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    $_SESSION['error_message'] = "ID tidak valid.";
    header('Location: daftar_followup.php');
    exit;
}

// Cek apakah data ada
$cek = mysqli_query($koneksi, "SELECT id FROM jejak_follow_up WHERE id = $id");
if (mysqli_num_rows($cek) == 0) {
    $_SESSION['error_message'] = "Data follow up tidak ditemukan.";
    header('Location: daftar_followup.php');
    exit;
}

// Proses hapus
$hapus = mysqli_query($koneksi, "DELETE FROM jejak_follow_up WHERE id = $id");
if ($hapus) {
    $_SESSION['success_message'] = "Data follow up berhasil dihapus.";
} else {
    $_SESSION['error_message'] = "Gagal menghapus data follow up.";
}

header('Location: daftar_followup.php');
exit;