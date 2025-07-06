<?php
include '../routes/config.php';

// Tambah data
if (isset($_POST['tambah'])) {
    $bank = $_POST['bank'];
    $no_rek = $_POST['no_rek'];
    $stmt = $conn->prepare("INSERT INTO akun_bank (bank, no_rek) VALUES (?, ?)");
    $stmt->bind_param("ss", $bank, $no_rek);
    $stmt->execute();
}

// Edit data
if (isset($_POST['update'])) {
    $id = $_POST['id_bank'];
    $bank = $_POST['bank'];
    $no_rek = $_POST['no_rek'];
    $stmt = $conn->prepare("UPDATE akun_bank SET bank=?, no_rek=? WHERE id_bank=?");
    $stmt->bind_param("ssi", $bank, $no_rek, $id);
    $stmt->execute();
}

// Hapus data
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM akun_bank WHERE id_bank = $id");
}
?>

<h2>Data bank Pemasukan</h2>
<form method="POST">
    <input type="hidden" name="id_bank" id="id_bank">
    <input type="text" name="bank" id="bank" placeholder="Kode bank" required>
    <input type="text" name="no_rek" id="no_rek" placeholder="Nama Kelompok" required>
    <button type="submit" name="tambah" id="btnTambah">Tambah</button>
    <button type="submit" name="update" id="btnUpdate" style="display:none;">Update</button>
</form>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>No</th>
        <th>bank</th>
        <th>Nama Kelompok</th>
        <th>Aksi</th>
    </tr>
    <?php
    $no = 1;
    $q = $conn->query("SELECT * FROM akun_bank");
    while ($r = $q->fetch_assoc()):
    ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= $r['bank'] ?></td>
        <td><?= $r['no_rek'] ?></td>
        <td>
            <button onclick="editAkun(<?= $r['id_bank'] ?>, '<?= $r['bank'] ?>', '<?= $r['no_rek'] ?>')">Edit</button>
            <a href="?hapus=<?= $r['id_bank'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<script>
function editAkun(id, bank, kelompok) {
    document.getElementById('id_bank').value = id;
    document.getElementById('bank').value = bank;
    document.getElementById('no_rek').value = kelompok;
    document.getElementById('btnTambah').style.display = 'none';
    document.getElementById('btnUpdate').style.display = 'inline';
}
</script>
