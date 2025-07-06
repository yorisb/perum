<?php
include '../routes/config.php';

// Tambah data
if (isset($_POST['tambah'])) {
    $akun = $_POST['akun'];
    $nama_kelompok = $_POST['nama_kelompok'];
    $stmt = $conn->prepare("INSERT INTO akun_pemasukan (akun, nama_kelompok) VALUES (?, ?)");
    $stmt->bind_param("ss", $akun, $nama_kelompok);
    $stmt->execute();
}

// Edit data
if (isset($_POST['update'])) {
    $id = $_POST['id_akun_pemasukan'];
    $akun = $_POST['akun'];
    $nama_kelompok = $_POST['nama_kelompok'];
    $stmt = $conn->prepare("UPDATE akun_pemasukan SET akun=?, nama_kelompok=? WHERE id_akun_pemasukan=?");
    $stmt->bind_param("ssi", $akun, $nama_kelompok, $id);
    $stmt->execute();
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM akun_pemasukan WHERE id_akun_pemasukan = $id");
}
?>

<h2>Data Akun Pemasukan</h2>
<form method="POST">
    <input type="hidden" name="id_akun_pemasukan" id="id_akun_pemasukan">
    <input type="text" name="akun" id="akun" placeholder="Kode Akun" required>
    <input type="text" name="nama_kelompok" id="nama_kelompok" placeholder="Nama Kelompok" required>
    <button type="submit" name="tambah" id="btnTambah">Tambah</button>
    <button type="submit" name="update" id="btnUpdate" style="display:none;">Update</button>
</form>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>No</th>
        <th>Akun</th>
        <th>Nama Kelompok</th>
        <th>Aksi</th>
    </tr>
    <?php
    $no = 1;
    $q = $conn->query("SELECT * FROM akun_pemasukan");
    while ($r = $q->fetch_assoc()):
    ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= $r['akun'] ?></td>
        <td><?= $r['nama_kelompok'] ?></td>
        <td>
            <button onclick="editAkun(<?= $r['id_akun_pemasukan'] ?>, '<?= $r['akun'] ?>', '<?= $r['nama_kelompok'] ?>')">Edit</button>
            <a href="?hapus=<?= $r['id_akun_pemasukan'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<script>
function editAkun(id, akun, kelompok) {
    document.getElementById('id_akun_pemasukan').value = id;
    document.getElementById('akun').value = akun;
    document.getElementById('nama_kelompok').value = kelompok;
    document.getElementById('btnTambah').style.display = 'none';
    document.getElementById('btnUpdate').style.display = 'inline';
}
</script>
