<?php
session_start();

// Cek apakah user belum login, redirect ke login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'routes/config.php';
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Generate kode otomatis
$result = $conn->query("SELECT MAX(kode) as kode_terakhir FROM calon_konsumen");
$row = $result->fetch_assoc();
$kode_terakhir = $row['kode_terakhir'];

if ($kode_terakhir) {
    $angka = (int) substr($kode_terakhir, 1);
    $angka_baru = $angka + 1;
    $kode_baru = 'K' . str_pad($angka_baru, 6, '0', STR_PAD_LEFT);
} else {
    $kode_baru = 'K000001';
}

// Fungsi untuk upload file
function uploadFile($fileInput, $target_dir) {
    if (isset($_FILES[$fileInput]) && $_FILES[$fileInput]['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES[$fileInput];
        
        // Pastikan direktori target ada
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Generate nama file unik untuk menghindari overwrite
        $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $newFileName = uniqid() . '.' . $fileExtension;
        $target_file = rtrim($target_dir, '/') . '/' . $newFileName;
        
        // Validasi file sebelum upload
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        $maxFileSize = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception("Jenis file tidak diizinkan. Hanya JPEG, PNG, dan PDF yang diterima.");
        }
        
        if ($file['size'] > $maxFileSize) {
            throw new Exception("Ukuran file terlalu besar. Maksimal 2MB.");
        }
        
        // Coba upload file
        if (move_uploaded_file($file['tmp_name'], $target_file)) {
            return $target_file;
        } else {
            throw new Exception("Gagal mengupload file.");
        }
    }
    return '';
}

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Bersihkan input
    $kode               = $conn->real_escape_string($_POST['kode']);
    $npwp               = $conn->real_escape_string($_POST['npwp']);
    $nama_lengkap       = $conn->real_escape_string($_POST['nama_lengkap']);
    $no_hp              = $conn->real_escape_string($_POST['no_hp']);
    $kartu_id           = $conn->real_escape_string($_POST['kartu_id']);
    $no_kartu_id        = $conn->real_escape_string($_POST['no_kartu_id']);
    $alamat_lengkap     = $conn->real_escape_string($_POST['alamat_lengkap']);
    $ket_boking         = $conn->real_escape_string($_POST['ket_boking']);
    $email              = $conn->real_escape_string($_POST['email']);
    $gaji               = $conn->real_escape_string($_POST['gaji']);

    $pekerjaan          = $conn->real_escape_string($_POST['pekerjaan']);
    $nama_kantor        = $conn->real_escape_string($_POST['nama_kantor']);
    $alamat_kantor      = $conn->real_escape_string($_POST['alamat_kantor']);
    $telp_kantor        = $conn->real_escape_string($_POST['telp_kantor']);
    $ket_kerja          = $conn->real_escape_string($_POST['ket_kerja']);

    $status_pasangan    = $conn->real_escape_string($_POST['status_pasangan']);
    $nama_pasangan      = $conn->real_escape_string($_POST['nama_pasangan']);
    $hp_pasangan        = $conn->real_escape_string($_POST['hp_pasangan']);
    $kerja_pasangan     = $conn->real_escape_string($_POST['kerja_pasangan']);
    $alamat_kerja_pasangan = $conn->real_escape_string($_POST['alamat_kerja_pasangan']);
    $ket_pasangan       = $conn->real_escape_string($_POST['ket_pasangan']);

    $nama_keluarga      = $conn->real_escape_string($_POST['nama_keluarga']);
    $hubungan_keluarga  = $conn->real_escape_string($_POST['hubungan_keluarga']);
    $telp_keluarga      = $conn->real_escape_string($_POST['telp_keluarga']);
    $alamat_keluarga    = $conn->real_escape_string($_POST['alamat_keluarga']);

    try {
        // Upload dokumen
        $scan_ktp = uploadFile('scan_ktp', 'uploads/ktp/');
        $scan_kk = uploadFile('scan_kk', 'uploads/kk/');
        $scan_slip_gaji = uploadFile('scan_slip_gaji', 'uploads/slip_gaji/');
        $scan_npwp = uploadFile('scan_npwp', 'uploads/npwp/');

        // Gunakan prepared statement untuk keamanan
        $stmt = $conn->prepare("INSERT INTO calon_konsumen (
            kode, npwp, nama_lengkap, no_hp, kartu_id, no_kartu_id, scan_ktp, scan_kk, scan_slip_gaji, scan_npwp,
            alamat_lengkap, ket_boking, email, gaji, pekerjaan, nama_kantor, alamat_kantor, telp_kantor, ket_kerja,
            status_pasangan, nama_pasangan, hp_pasangan, kerja_pasangan, alamat_kerja_pasangan, ket_pasangan,
            nama_keluarga, hubungan_keluarga, telp_keluarga, alamat_keluarga
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("sssssssssssssssssssssssssssss", 
            $kode, $npwp, $nama_lengkap, $no_hp, $kartu_id, $no_kartu_id, $scan_ktp, $scan_kk, $scan_slip_gaji, $scan_npwp,
            $alamat_lengkap, $ket_boking, $email, $gaji, $pekerjaan, $nama_kantor, $alamat_kantor, $telp_kantor, $ket_kerja,
            $status_pasangan, $nama_pasangan, $hp_pasangan, $kerja_pasangan, $alamat_kerja_pasangan, $ket_pasangan,
            $nama_keluarga, $hubungan_keluarga, $telp_keluarga, $alamat_keluarga
        );

        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Data konsumen berhasil ditambahkan";
            header("Location: daftar_konsumen.php");
            exit();
        } else {
            $error_message = "Error: " . $stmt->error;
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Unit Perumahan - Input Calon Konsumen</title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js" defer></script>
</head>
<body class="bg-gray-200 min-h-screen flex">

  <!-- Sidebar Toggle Button -->
  <button id="sidebarToggle" class="text-gray-500 bg-white p-2 rounded-md border-2 border-gray-500 fixed top-4 left-4 z-50">
    &#9776; <!-- Hamburger Icon -->
  </button>

  <!-- Sidebar -->
  <?php include 'templates/sidebar.php'; ?>

  <!-- Main Content -->
  <div id="main-content" class="flex-1 ml-64 p-6 transition-all duration-300 ease-out">

    <!-- Navbar -->
    <div id="navbar" class=" rounded-md fixed top-0 left-0 w-full z-10 transition-all duration-300 ease-out ml-64">
      <?php include 'templates/navbar.php'; ?>
    </div>

    <!-- Content -->
<div class="max-w-7xl mx-auto mt-16">
    <div class="rounded-lg p-6">
    <header class="bg-white shadow-md rounded-lg p-6 mb-4">
        <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">Formulir Pendaftaran Konsumen Baru</h2>
        <!-- Tombol Kembali -->
        <div class="mb-4 text-right">
            <a href="daftar_konsumen.php" class="inline-flex items-center px-4 py-2 bg-red-200 hover:bg-red-300 text-red-800 text-sm font-medium rounded-md shadow-sm transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Konsumen
            </a>
        </div>
    </header>
        
        <?php if (isset($error_message)): ?>
            <div class="mb-4 text-sm text-red-700 bg-red-100 rounded-lg"><?= $error_message ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <!-- Data Pribadi Section -->
            <div class="bg-white space-y-4 p-4 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold">Informasi Pribadi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="kode" class="block mb-2 text-sm font-medium text-gray-900">Kode Konsumen</label>
                        <input type="text" id="kode" name="kode" value="<?= $kode_baru ?>" readonly class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="npwp" class="block mb-2 text-sm font-medium text-gray-900">NPWP</label>
                        <input type="text" id="npwp" name="npwp" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_lengkap" class="block mb-2 text-sm font-medium text-gray-900 required-field">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="no_hp" class="block mb-2 text-sm font-medium text-gray-900 required-field">Nomor HP</label>
                        <input type="text" id="no_hp" name="no_hp" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="kartu_id" class="block mb-2 text-sm font-medium text-gray-900 required-field">Jenis Identitas</label>
                        <select id="kartu_id" name="kartu_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">-- Pilih --</option>
                            <option value="KTP">KTP</option>
                            <option value="SIM">SIM</option>
                        </select>
                    </div>
                    <div>
                        <label for="no_kartu_id" class="block mb-2 text-sm font-medium text-gray-900 required-field">Nomor Identitas</label>
                        <input type="text" id="no_kartu_id" name="no_kartu_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="scan_ktp" class="block mb-2 text-sm font-medium text-gray-900 required-field">Scan KTP</label>
                        <input type="file" id="scan_ktp" name="scan_ktp" required class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    </div>
                </div>
                
                <div>
                    <label for="alamat_lengkap" class="block mb-2 text-sm font-medium text-gray-900 required-field">Alamat Lengkap</label>
                    <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" required class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 required-field">Email</label>
                        <input type="email" id="email" name="email" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="gaji" class="block mb-2 text-sm font-medium text-gray-900 required-field">Gaji</label>
                        <input type="number" id="gaji" name="gaji" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
                
                <div>
                    <label for="ket_boking" class="block mb-2 text-sm font-medium text-gray-900">Keterangan Booking</label>
                    <textarea id="ket_boking" name="ket_boking" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            
            <!-- Data Pekerjaan Section -->
            <div class="bg-white space-y-4 p-4 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold">Informasi Pekerjaan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="pekerjaan" class="block mb-2 text-sm font-medium text-gray-900">Pekerjaan</label>
                        <select id="pekerjaan" name="pekerjaan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">-- Pilih --</option>
                            <option value="PNS">PNS</option>
                            <option value="Pegawai Swasta">Pegawai Swasta</option>
                            <option value="Wiraswasta">Wiraswasta</option>
                            <option value="Petani">Petani</option>
                            <option value="Nelayan">Nelayan</option>
                            <option value="Buruh">Buruh</option>
                            <option value="Tidak Bekerja">Tidak Bekerja</option>
                        </select>
                    </div>
                    <div>
                        <label for="nama_kantor" class="block mb-2 text-sm font-medium text-gray-900">Nama Perusahaan</label>
                        <input type="text" id="nama_kantor" name="nama_kantor" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
                
                <div>
                    <label for="alamat_kantor" class="block mb-2 text-sm font-medium text-gray-900">Alamat Perusahaan</label>
                    <textarea id="alamat_kantor" name="alamat_kantor" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="telp_kantor" class="block mb-2 text-sm font-medium text-gray-900">Telepon Perusahaan</label>
                        <input type="text" id="telp_kantor" name="telp_kantor" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="ket_kerja" class="block mb-2 text-sm font-medium text-gray-900">Keterangan Pekerjaan</label>
                        <input type="text" id="ket_kerja" name="ket_kerja" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
            </div>
            
            <!-- Data Pasangan Section -->
            <div class="bg-white space-y-4 p-4 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold">Informasi Pasangan</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="status_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Status Pernikahan</label>
                        <select id="status_pasangan" name="status_pasangan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">-- Pilih --</option>
                            <option value="Menikah">Menikah</option>
                            <option value="Belum Menikah">Belum Menikah</option>
                        </select>
                    </div>
                    <div>
                        <label for="nama_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Nama Pasangan</label>
                        <input type="text" id="nama_pasangan" name="nama_pasangan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="hp_pasangan" class="block mb-2 text-sm font-medium text-gray-900">HP Pasangan</label>
                        <input type="text" id="hp_pasangan" name="hp_pasangan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="kerja_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Pekerjaan Pasangan</label>
                        <select id="kerja_pasangan" name="kerja_pasangan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">-- Pilih --</option>
                            <option value="PNS">PNS</option>
                            <option value="Pegawai Swasta">Pegawai Swasta</option>
                            <option value="Wiraswasta">Wiraswasta</option>
                            <option value="Petani">Petani</option>
                            <option value="Nelayan">Nelayan</option>
                            <option value="Buruh">Buruh</option>
                            <option value="Tidak Bekerja">Tidak Bekerja</option>
                        </select>
                    </div>
                    <div>
                        <label for="alamat_kerja_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Alamat Kerja Pasangan</label>
                        <input type="text" id="alamat_kerja_pasangan" name="alamat_kerja_pasangan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
                
                <div>
                    <label for="ket_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Keterangan Pasangan</label>
                    <textarea id="ket_pasangan" name="ket_pasangan" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            
            <!-- Data Keluarga Section -->
            <div class="bg-white space-y-4 p-4 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold">Informasi Keluarga</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="nama_keluarga" class="block mb-2 text-sm font-medium text-gray-900">Nama Keluarga</label>
                        <input type="text" id="nama_keluarga" name="nama_keluarga" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="hubungan_keluarga" class="block mb-2 text-sm font-medium text-gray-900">Hubungan</label>
                        <select id="hubungan_keluarga" name="hubungan_keluarga" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">-- Pilih --</option>
                            <option value="Ayah">Ayah</option>
                            <option value="Ibu">Ibu</option>
                            <option value="Saudara">Saudara</option>
                            <option value="Teman">Teman</option>
                        </select>
                    </div>
                    <div>
                        <label for="telp_keluarga" class="block mb-2 text-sm font-medium text-gray-900">Telepon Keluarga</label>
                        <input type="text" id="telp_keluarga" name="telp_keluarga" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
                
                <div>
                    <label for="alamat_keluarga" class="block mb-2 text-sm font-medium text-gray-900">Alamat Keluarga</label>
                    <textarea id="alamat_keluarga" name="alamat_keluarga" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            
            <!-- Informasi Dokumen Section -->
            <div class="bg-white space-y-4 p-4 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold">Informasi Dokumen</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="scan_kk" class="block mb-2 text-sm font-medium text-gray-900">Scan Kartu Keluarga (KK)</label>
                        <input type="file" id="scan_kk" name="scan_kk" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    </div>
                    <div>
                        <label for="scan_slip_gaji" class="block mb-2 text-sm font-medium text-gray-900">Scan Slip Gaji</label>
                        <input type="file" id="scan_slip_gaji" name="scan_slip_gaji" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    </div>
                    <div>
                        <label for="scan_npwp" class="block mb-2 text-sm font-medium text-gray-900">Scan NPWP</label>
                        <input type="file" id="scan_npwp" name="scan_npwp" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Simpan Data</button>
            </div>
        </form>
    </div>
</div>
<style>
    .required-field:after {
        content: " *";
        color: red;
    }
</style>

  <!-- Flowbite -->
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>

  <!-- Sidebar Toggle Script -->
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const sidebar = document.getElementById('sidebar');
      const sidebarToggle = document.getElementById('sidebarToggle');
      const mainContent = document.getElementById('main-content');
      const navbar = document.getElementById('navbar');

      sidebarToggle.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');

        const isHidden = sidebar.classList.contains('-translate-x-full');

        mainContent.classList.toggle('ml-64', !isHidden);
        mainContent.classList.toggle('ml-0', isHidden);

        navbar.classList.toggle('ml-64', !isHidden);
        navbar.classList.toggle('pl-16', isHidden);
      });
    });
  </script>

  <!-- Styling -->
  <style>
    #sidebar {
      transition: transform 0.3s ease-out;
      height: 100vh; /* Pastikan sidebar memiliki tinggi penuh layar */
      overflow-y: auto; /* Aktifkan scroll secara vertikal */
      overflow-x: hidden; /* Sembunyikan scroll horizontal jika tidak diperlukan */
    }

    .-translate-x-full {
      transform: translateX(-100%);
    }

    .ml-0 {
      margin-left: 0 !important;
    }

    .ml-64 {
      margin-left: 16rem !important; /* 64 * 0.25rem */
    }

    .pl-16 {
      padding-left: 4rem !important; /* 16 * 0.25rem */
    }

    #main-content, #navbar {
      transition: margin-left 0.3s ease-out, padding-left 0.3s ease-out;
    }
  </style>
</body>
</html>