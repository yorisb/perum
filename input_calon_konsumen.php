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

    // Upload KTP
    $scan_ktp = '';
    if (isset($_FILES['scan_ktp']) && $_FILES['scan_ktp']['error'] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($_FILES['scan_ktp']['name']);
        if (move_uploaded_file($_FILES['scan_ktp']['tmp_name'], $target_file)) {
            $scan_ktp = $target_file;
        }
    }

    // Gunakan prepared statement untuk keamanan
    $stmt = $conn->prepare("INSERT INTO calon_konsumen (
        kode, npwp, nama_lengkap, no_hp, kartu_id, no_kartu_id, scan_ktp, alamat_lengkap, ket_boking, email, gaji,
        pekerjaan, nama_kantor, alamat_kantor, telp_kantor, ket_kerja,
        status_pasangan, nama_pasangan, hp_pasangan, kerja_pasangan, alamat_kerja_pasangan, ket_pasangan,
        nama_keluarga, hubungan_keluarga, telp_keluarga, alamat_keluarga
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("ssssssssssssssssssssssssss", 
        $kode, $npwp, $nama_lengkap, $no_hp, $kartu_id, $no_kartu_id, $scan_ktp, $alamat_lengkap, $ket_boking, $email, $gaji,
        $pekerjaan, $nama_kantor, $alamat_kantor, $telp_kantor, $ket_kerja,
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
<body class="bg-gray-100 min-h-screen flex">

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
<div class="max-w-7xl mx-auto mt-20">
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-center mb-6">Customer Registration Form</h2>
        
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($conn->error)): ?>
            <div class="mb-4 text-sm text-red-700 bg-red-100 rounded-lg"><?= $conn->error ?></div>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <div class="mb-4 text-sm text-green-700 bg-green-100 rounded-lg">Data berhasil disimpan!</div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <!-- Personal Data Section -->
            <div class="bg-gray-100 space-y-4 p-4 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold">Personal Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="kode" class="block mb-2 text-sm font-medium text-gray-900">Customer Code</label>
                        <input type="text" id="kode" name="kode" value="<?= $kode_baru ?>" readonly class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="npwp" class="block mb-2 text-sm font-medium text-gray-900">NPWP</label>
                        <input type="text" id="npwp" name="npwp" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nama_lengkap" class="block mb-2 text-sm font-medium text-gray-900 required-field">Full Name</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="no_hp" class="block mb-2 text-sm font-medium text-gray-900 required-field">Phone Number</label>
                        <input type="text" id="no_hp" name="no_hp" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="kartu_id" class="block mb-2 text-sm font-medium text-gray-900 required-field">ID Type</label>
                        <select id="kartu_id" name="kartu_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">-- Select --</option>
                            <option value="KTP">KTP</option>
                            <option value="SIM">SIM</option>
                        </select>
                    </div>
                    <div>
                        <label for="no_kartu_id" class="block mb-2 text-sm font-medium text-gray-900 required-field">ID Number</label>
                        <input type="text" id="no_kartu_id" name="no_kartu_id" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="scan_ktp" class="block mb-2 text-sm font-medium text-gray-900">ID Scan</label>
                        <input type="file" id="scan_ktp" name="scan_ktp" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    </div>
                </div>
                
                <div>
                    <label for="alamat_lengkap" class="block mb-2 text-sm font-medium text-gray-900 required-field">Full Address</label>
                    <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" required class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 required-field">Email</label>
                        <input type="email" id="email" name="email" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="gaji" class="block mb-2 text-sm font-medium text-gray-900 required-field">Salary</label>
                        <input type="number" id="gaji" name="gaji" required class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
                
                <div>
                    <label for="ket_boking" class="block mb-2 text-sm font-medium text-gray-900">Notes/Booking</label>
                    <textarea id="ket_boking" name="ket_boking" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            
            <!-- Employment Data Section -->
            <div class="bg-gray-100 space-y-4 p-4 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold">Employment Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="pekerjaan" class="block mb-2 text-sm font-medium text-gray-900">Occupation</label>
                        <select id="pekerjaan" name="pekerjaan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">-- Select --</option>
                            <option value="PNS">PNS</option>
                            <option value="Pegawai Swasta">Private Employee</option>
                            <option value="Wiraswasta">Entrepreneur</option>
                            <option value="Petani">Farmer</option>
                            <option value="Nelayan">Fisherman</option>
                            <option value="Buruh">Laborer</option>
                            <option value="Tidak Bekerja">Not Working</option>
                        </select>
                    </div>
                    <div>
                        <label for="nama_kantor" class="block mb-2 text-sm font-medium text-gray-900">Company Name</label>
                        <input type="text" id="nama_kantor" name="nama_kantor" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
                
                <div>
                    <label for="alamat_kantor" class="block mb-2 text-sm font-medium text-gray-900">Company Address</label>
                    <textarea id="alamat_kantor" name="alamat_kantor" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="telp_kantor" class="block mb-2 text-sm font-medium text-gray-900">Company Phone</label>
                        <input type="text" id="telp_kantor" name="telp_kantor" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="ket_kerja" class="block mb-2 text-sm font-medium text-gray-900">Work Notes</label>
                        <input type="text" id="ket_kerja" name="ket_kerja" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
            </div>
            
            <!-- Spouse Data Section -->
            <div class="bg-gray-100 space-y-4 p-4 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold">Spouse Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="status_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Marital Status</label>
                        <select id="status_pasangan" name="status_pasangan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">-- Select --</option>
                            <option value="Menikah">Married</option>
                            <option value="Belum Menikah">Single</option>
                        </select>
                    </div>
                    <div>
                        <label for="nama_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Spouse Name</label>
                        <input type="text" id="nama_pasangan" name="nama_pasangan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="hp_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Spouse Phone</label>
                        <input type="text" id="hp_pasangan" name="hp_pasangan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="kerja_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Spouse Occupation</label>
                        <select id="kerja_pasangan" name="kerja_pasangan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">-- Select --</option>
                            <option value="PNS">PNS</option>
                            <option value="Pegawai Swasta">Private Employee</option>
                            <option value="Wiraswasta">Entrepreneur</option>
                            <option value="Petani">Farmer</option>
                            <option value="Nelayan">Fisherman</option>
                            <option value="Buruh">Laborer</option>
                            <option value="Tidak Bekerja">Not Working</option>
                        </select>
                    </div>
                    <div>
                        <label for="alamat_kerja_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Spouse Work Address</label>
                        <input type="text" id="alamat_kerja_pasangan" name="alamat_kerja_pasangan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
                
                <div>
                    <label for="ket_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Spouse Notes</label>
                    <textarea id="ket_pasangan" name="ket_pasangan" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            
            <!-- Family Data Section -->
            <div class="bg-gray-100 space-y-4 p-4 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold">Family Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label for="nama_keluarga" class="block mb-2 text-sm font-medium text-gray-900">Family Member Name</label>
                        <input type="text" id="nama_keluarga" name="nama_keluarga" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                    <div>
                        <label for="hubungan_keluarga" class="block mb-2 text-sm font-medium text-gray-900">Relationship</label>
                        <select id="hubungan_keluarga" name="hubungan_keluarga" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">-- Select --</option>
                            <option value="Ayah">Father</option>
                            <option value="Ibu">Mother</option>
                            <option value="Saudara">Sibling</option>
                            <option value="Teman">Friend</option>
                        </select>
                    </div>
                    <div>
                        <label for="telp_keluarga" class="block mb-2 text-sm font-medium text-gray-900">Family Phone</label>
                        <input type="text" id="telp_keluarga" name="telp_keluarga" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    </div>
                </div>
                
                <div>
                    <label for="alamat_keluarga" class="block mb-2 text-sm font-medium text-gray-900">Family Address</label>
                    <textarea id="alamat_keluarga" name="alamat_keluarga" rows="2" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
            </div>
            
            <div class="text-center">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Submit Form</button>
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