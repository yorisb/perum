<?php
include '../routes/config.php';

// Ambil data unit
$data_unit = $conn->query("SELECT id, nama_unit FROM unit_properti");

// Ambil data akun pengeluaran (gunakan `akun_pemasukan` jika sama)
$data_akun = $conn->query("SELECT id_akun_pengeluaran, akun, nama_kelompok FROM akun_pengeluaran");

// Ambil data bank
$data_bank = $conn->query("SELECT id_bank, bank, no_rek FROM akun_bank");

// Proses simpan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'];
    $asal = $_POST['asal'];
    $unit_id = $_POST['unit_id'];
    $akun_id = $_POST['akun_id'];
    $bank_id = $_POST['bank_id'];
    $keperluan = $_POST['keperluan'];
    $jumlah = $_POST['jumlah'];
    $no_tanda_terima = $_POST['no_tanda_terima'];
    $jenis_pembayaran = $_POST['jenis_pembayaran'];
    $no_faktur = $_POST['no_faktur'];

    // Upload file
    $file_upload = '';
    if ($_FILES['file_upload']['name']) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_upload = $target_dir . basename($_FILES["file_upload"]["name"]);
        move_uploaded_file($_FILES["file_upload"]["tmp_name"], $file_upload);
    }

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO pengeluaran (tanggal, asal, unit_id, akun_id, bank_id, keperluan, jumlah, no_tanda_terima, file_upload, jenis_pembayaran, no_faktur) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiissdsss", $tanggal, $asal, $unit_id, $akun_id, $bank_id, $keperluan, $jumlah, $no_tanda_terima, $file_upload, $jenis_pembayaran, $no_faktur);

    if ($stmt->execute()) {
        echo "<script>alert('Data pengeluaran berhasil disimpan!');</script>";
    } else {
        echo "Gagal menyimpan data: " . $stmt->error;
    }
}
?>

<h2>Form Input Pengeluaran</h2>
<form method="POST" enctype="multipart/form-data">
    <label>Tanggal Pengeluaran:</label><br>
    <input type="date" name="tanggal" required><br><br>

    <label>Asal (Diberikan Kepada):</label><br>
    <input type="text" name="asal" required><br><br>

    <label>Nama Unit/Kavling:</label><br>
    <select name="unit_id" required>
        <option value="">-- Pilih Unit --</option>
        <?php while ($row = $data_unit->fetch_assoc()): ?>
            <option value="<?= $row['id'] ?>"><?= $row['nama_unit'] ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Kelompok Akun Pengeluaran:</label><br>
    <select name="akun_id" required>
        <option value="">-- Pilih Akun --</option>
        <?php while ($row = $data_akun->fetch_assoc()): ?>
            <option value="<?= $row['id_akun_pengeluaran'] ?>"><?= $row['akun'] ?></option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Bank:</label><br>
    <select name="bank_id" required>
        <option value="">-- Pilih Bank --</option>
        <?php while ($row = $data_bank->fetch_assoc()): ?>
            <option value="<?= $row['id_bank'] ?>"><?= $row['bank'] ?> (<?= $row['no_rek'] ?>)</option>
        <?php endwhile; ?>
    </select><br><br>

    <label>Keperluan:</label><br>
    <textarea name="keperluan" rows="3" cols="40"></textarea><br><br>

    <label>Jumlah Pengeluaran:</label><br>
    <input type="number" name="jumlah" step="0.01" required><br><br>

    <label>No Tanda Terima:</label><br>
    <input type="text" name="no_tanda_terima"><br><br>

    <label>No Faktur:</label><br>
    <input type="text" name="no_faktur"><br><br>

    <label>Jenis Pembayaran:</label><br>
    <select name="jenis_pembayaran" required>
        <option value="">-- Pilih Jenis --</option>
        <option value="HUTANG">HUTANG</option>
        <option value="NON HUTANG">NON HUTANG</option>
    </select><br><br>

    <label>Upload File:</label><br>
    <input type="file" name="file_upload"><br><br>

    <button type="submit">Simpan Pengeluaran</button>
</form>
