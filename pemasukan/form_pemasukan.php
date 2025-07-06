<?php
// Koneksi ke database
include '../routes/config.php';

// Ambil data unit
$data_unit = $conn->query("SELECT id, nama_unit FROM unit_properti");

// Ambil data akun pemasukan
$data_akun = $conn->query("SELECT id_akun_pemasukan, nama_kelompok FROM akun_pemasukan");

// Ambil data bank
$data_bank = $conn->query("SELECT id_bank, bank, no_rek FROM akun_bank");

// Proses form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $asal = $_POST['asal'];
    $unit_id = $_POST['unit_id'];
    $akun_id = $_POST['akun_id'];
    $bank_id = $_POST['bank_id'];
    $keterangan = $_POST['keterangan'];
    $jumlah = $_POST['jumlah'];
    $no_tanda_terima = $_POST['no_tanda_terima'];

    // Handle upload file
    $file_upload = '';
    if ($_FILES['file_upload']['name']) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_upload = $target_dir . basename($_FILES["file_upload"]["name"]);
        move_uploaded_file($_FILES["file_upload"]["tmp_name"], $file_upload);
    }

    // Simpan ke tabel pemasukan
    $stmt = $conn->prepare("INSERT INTO pemasukan (tanggal, asal, unit_id, akun_id, bank_id, keterangan, jumlah, no_tanda_terima, file_upload) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiissds", $tanggal, $asal, $unit_id, $akun_id, $bank_id, $keterangan, $jumlah, $no_tanda_terima, $file_upload);

    if ($stmt->execute()) {
        echo "<script>alert('Data pemasukan berhasil disimpan!');</script>";
    } else {
        echo "Gagal menyimpan data: " . $stmt->error;
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <label>Tanggal Pemasukan:</label><br>
    <input type="date" name="tanggal" required><br><br>

    <label>Terima Dari (Asal):</label><br>
    <input type="text" name="asal" required><br><br>

    <label>Nama Unit/Kavling:</label><br>
    <select name="unit_id" required>
        <option value="">-- Pilih Unit --</option>
        <?php while ($row = $data_unit->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= $row['nama_unit'] ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Kelompok Pemasukan:</label><br>
    <select name="akun_id" required>
        <option value="">-- Pilih Kelompok --</option>
        <?php while ($row = $data_akun->fetch_assoc()): ?>
            <option value="<?= $row['id_akun_pemasukan'] ?>"><?= $row['nama_kelompok'] ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Bank:</label><br>
    <select name="bank_id" required>
        <option value="">-- Pilih Bank --</option>
        <?php while ($row = $data_bank->fetch_assoc()): ?>
            <option value="<?= $row['id_bank'] ?>"><?= $row['bank'] ?> (<?= $row['no_rek'] ?>)</option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Keterangan:</label><br>
    <textarea name="keterangan" rows="3" cols="40"></textarea><br><br>

    <label>Jumlah Pemasukan:</label><br>
    <input type="number" name="jumlah" step="0.01" required><br><br>

    <label>No Tanda Terima:</label><br>
    <input type="text" name="no_tanda_terima"><br><br>

    <label>Upload File:</label><br>
    <input type="file" name="file_upload"><br><br>

    <button type="submit">Simpan</button>
</form>
