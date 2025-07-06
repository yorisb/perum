<?php
$conn = new mysqli("srv143.niagahoster.com", "n1572337_developer", "bad_cookies8080", "n1572337_developer");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $no_transaksi = $_POST['no_transaksi'];
    $tgl_transaksi = $_POST['tgl_transaksi'];
    $id_konsumen = $_POST['id_konsumen'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $harga_kesepakatan = $_POST['harga_kesepakatan'];
    $kode_konsumen = $_POST['kode_konsumen'];
    $luas_tanah = $_POST['luas_tanah'];
    $harga_per_m2 = $_POST['harga_per_m2'];
    $total_harga_penambahan = $_POST['total_harga_penambahan'];
    $penambahan_lain = $_POST['penambahan_lain'];
    $total_harga_penambahan_lain = $_POST['total_harga_penambahan_lain'];
    $total_penambahan = $_POST['total_penambahan'];
    $total_setelah_penambahan = $_POST['total_setelah_penambahan'];
    $nama_unit = $_POST['nama_unit'];
    $type = $_POST['type'];
    $luas_bangunan = $_POST['luas_bangunan'];
    $tanda_jadi = $_POST['tanda_jadi'];
    $status_tanda_jadi = $_POST['status_tanda_jadi'];
    $periode_uang_muka = $_POST['periode_uang_muka'];
    $harga = $_POST['harga'];
    $bonus = $_POST['bonus'];
    $uang_muka = $_POST['uang_muka'];
    $total_akhir = $_POST['total_akhir'];
    $cara_pembayaran = $_POST['cara_pembayaran'];
    $rencana_tgl_bayar_tanda_jadi = $_POST['rencana_tgl_bayar_tanda_jadi'];
    $rencana_tgl_bayar_uang_muka = $_POST['rencana_tgl_bayar_uang_muka'];
    $rencana_tgl_bayar_pembayaran = $_POST['rencana_tgl_bayar_pembayaran'];

    // Query untuk menyimpan transaksi (hapus periode_cicilan dan cicilan)
$sql_transaksi = "INSERT INTO transaksi (
    no_transaksi, tgl_transaksi, id_konsumen, nama_lengkap, harga_kesepakatan, kode_konsumen, luas_tanah,
    harga_per_m2, total_harga_penambahan, penambahan_lain, total_harga_penambahan_lain,
    total_penambahan, total_setelah_penambahan, nama_unit, type, luas_bangunan, tanda_jadi,
    status_tanda_jadi, periode_uang_muka, harga, bonus, uang_muka, total_akhir,
    cara_pembayaran, rencana_tgl_bayar_tanda_jadi,
    rencana_tgl_bayar_uang_muka, rencana_tgl_bayar_pembayaran
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql_transaksi)) {
        $stmt->bind_param(
            "sssssssssssssssssssssssssss",
            $no_transaksi, $tgl_transaksi, $id_konsumen, $nama_lengkap, $harga_kesepakatan, $kode_konsumen, $luas_tanah,
            $harga_per_m2, $total_harga_penambahan, $penambahan_lain, $total_harga_penambahan_lain,
            $total_penambahan, $total_setelah_penambahan, $nama_unit, $type, $luas_bangunan, $tanda_jadi,
            $status_tanda_jadi, $periode_uang_muka, $harga, $bonus, $uang_muka, $total_akhir,
            $cara_pembayaran, $rencana_tgl_bayar_tanda_jadi,
            $rencana_tgl_bayar_uang_muka, $rencana_tgl_bayar_pembayaran
        );

        if ($stmt->execute()) {
            $transaksi_id = $stmt->insert_id;

            // Simpan data angsuran jika ada
            for ($i = 1; $i <= $periode_uang_muka; $i++) {
                $angsuran = $_POST['angsuran_' . $i] ?? 0;
                if ($angsuran > 0) {
                    $sql_angsuran = "INSERT INTO angsuran (transaksi_id, angsuran_ke, angsuran_amount) VALUES (?, ?, ?)";
                    if ($angsuran_stmt = $conn->prepare($sql_angsuran)) {
                        $angsuran_stmt->bind_param("iii", $transaksi_id, $i, $angsuran);
                        $angsuran_stmt->execute();
                    }
                }
            }

            // Update status unit_properti menjadi 1
            $sql_update_unit = "UPDATE unit_properti SET status = 1 WHERE nama_unit = ?";
            if ($update_stmt = $conn->prepare($sql_update_unit)) {
                $update_stmt->bind_param("s", $nama_unit);
                $update_stmt->execute();
            }

            // Redirect ke daftar_transaksi.php dengan pesan sukses
            header("Location: daftar_transaksi.php?success=1");
            exit();

            echo "Transaksi berhasil disimpan!";
        } else {
            echo "Error: " . $stmt->error;
        }
    } else {
        echo "Error: " . $conn->error;
    }

    $conn->close();
}
?>