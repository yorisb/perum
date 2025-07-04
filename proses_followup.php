<?php
// koneksi database
$conn = new mysqli("srv143.niagahoster.com", "n1572337_developer", "bad_cookies8080", "n1572337_developer");

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// ambil data
$calon_konsumen   = $_POST['calon_konsumen'];
$tgl_follow_up    = $_POST['tgl_follow_up'];
$melalui          = $_POST['melalui'];
$telp             = $_POST['telp'];
$keterangan       = $_POST['keterangan'];
$hasil            = $_POST['hasil'];
$status_progres   = $_POST['status_progres'];

// simpan ke database
$sql = "INSERT INTO jejak_follow_up (calon_konsumen, tgl_follow_up, melalui, telp, keterangan, hasil, status_progres)
        VALUES (?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $calon_konsumen, $tgl_follow_up, $melalui, $telp, $keterangan, $hasil, $status_progres);

if ($stmt->execute()) {
    // Redirect ke daftar_followup.php dengan pesan sukses
    header("Location: daftar_followup.php?success=1");
    exit(); // Pastikan tidak ada output lain sebelum redirect
} else {
    // Tampilkan pesan error dan tetap di halaman ini
    echo "<script>
            alert('Gagal menyimpan data: " . addslashes($stmt->error) . "');
            window.history.back();
          </script>";
}

$stmt->close();
$conn->close();
?>