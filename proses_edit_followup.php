<?php
session_start();
require_once 'koneksi.php';

// Cek login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Validasi input
$id              = isset($_POST['id']) ? intval($_POST['id']) : 0;
$calon_konsumen  = isset($_POST['calon_konsumen']) ? intval($_POST['calon_konsumen']) : 0;
$tgl_follow_up   = isset($_POST['tgl_follow_up']) ? trim($_POST['tgl_follow_up']) : '';
$melalui         = isset($_POST['melalui']) ? trim($_POST['melalui']) : '';
$keterangan      = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : '';
$hasil           = isset($_POST['hasil']) ? trim($_POST['hasil']) : '';
$status_progres  = isset($_POST['status_progres']) ? trim($_POST['status_progres']) : '';

// Validasi wajib
if (
    $id <= 0 ||
    $calon_konsumen <= 0 ||
    empty($tgl_follow_up) ||
    empty($melalui) ||
    empty($keterangan) ||
    empty($status_progres)
) {
    $_SESSION['error_message'] = "Data tidak lengkap!";
    header("Location: edit_followup.php?id=$id");
    exit;
}

// Update data
$query = "UPDATE jejak_follow_up SET 
            calon_konsumen = ?,
            tgl_follow_up = ?,
            melalui = ?,
            keterangan = ?,
            hasil = ?,
            status_progres = ?
          WHERE id = ?";
$stmt = $koneksi->prepare($query);
$stmt->bind_param(
    "isssssi",
    $calon_konsumen,
    $tgl_follow_up,
    $melalui,
    $keterangan,
    $hasil,
    $status_progres,
    $id
);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Data follow up berhasil diperbarui.";
    header("Location: daftar_followup.php");
    exit;
} else {
    $_SESSION['error_message'] = "Gagal memperbarui data. " . $koneksi->error;
    header("Location: edit_followup.php?id=$id");
    exit;
}
?>