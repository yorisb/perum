<?php
session_start();

// Cek apakah user belum login, redirect ke login
if (isset($_SESSION['username'])) {
    include 'routes/config.php';
    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}

// Ambil dan hapus pesan sukses dari session
$loginSuccess = '';
if (!empty($_SESSION['success_message'])) {
    $loginSuccess = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
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
        $target_dir = "pengeluaran/uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $file_upload = $target_dir . basename($_FILES["file_upload"]["name"]);
        move_uploaded_file($_FILES["file_upload"]["tmp_name"], $file_upload);
    }

    // Simpan ke database
    $stmt = $conn->prepare("INSERT INTO pengeluaran (tanggal, asal, unit_id, akun_id, bank_id, keperluan, jumlah, no_tanda_terima, file_upload, jenis_pembayaran, no_faktur) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiissdsss", $tanggal, $asal, $unit_id, $akun_id, $bank_id, $keperluan, $jumlah, $no_tanda_terima, $file_upload, $jenis_pembayaran, $no_faktur);

    if ($stmt->execute()) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    const toast = document.getElementById('success-toast');
                    toast.classList.remove('hidden');
                    setTimeout(() => {
                        toast.classList.add('hidden');
                    }, 5000);
                });
              </script>";
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    const toast = document.getElementById('error-toast');
                    toast.classList.remove('hidden');
                    setTimeout(() => {
                        toast.classList.add('hidden');
                    }, 5000);
                });
              </script>";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Unit Perumahan - Dashboard</title>
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
    <div class="mt-20">
      <?php if (!empty($loginSuccess)): ?>
        <div id="login-alert" class="fixed top-6 left-1/2 transform -translate-x-1/2 w-80 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md shadow-md flex items-start gap-3 text-sm animate-fade-in-down z-50 transition-opacity duration-500">
          <svg class="w-5 h-5 mt-0.5 text-green-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <div class="flex-1">
            <p class="font-semibold">Login berhasil!</p>
            <p class="text-xs"><?= htmlspecialchars($loginSuccess); ?></p>
          </div>
        </div>

        <script>
          setTimeout(() => {
            const alert = document.getElementById('login-alert');
            if (alert) {
              alert.classList.add('opacity-0');
              setTimeout(() => alert.remove(), 500);
            }
          }, 5000);
        </script>
      <?php endif; ?>
    </div>

    <div class="max-w-6xl mx-auto p-6 bg-white rounded-lg shadow-md">
    <!-- Success Toast -->
    <div id="success-toast" class="hidden fixed top-4 right-4 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg dark:bg-green-800 dark:text-green-200">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
            </svg>
            <span class="sr-only">Check icon</span>
        </div>
        <div class="ms-3 text-sm font-normal">Data pengeluaran berhasil disimpan!</div>
        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" onclick="this.parentElement.classList.add('hidden')">
            <span class="sr-only">Close</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </button>
    </div>

    <!-- Error Toast -->
    <div id="error-toast" class="hidden fixed top-4 right-4 flex items-center w-full max-w-xs p-4 mb-4 text-gray-500 bg-white rounded-lg shadow dark:text-gray-400 dark:bg-gray-800" role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-500 bg-red-100 rounded-lg dark:bg-red-800 dark:text-red-200">
            <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 11.793a1 1 0 1 1-1.414 1.414L10 11.414l-2.293 2.293a1 1 0 0 1-1.414-1.414L8.586 10 6.293 7.707a1 1 0 0 1 1.414-1.414L10 8.586l2.293-2.293a1 1 0 0 1 1.414 1.414L11.414 10l2.293 2.293Z"/>
            </svg>
            <span class="sr-only">Error icon</span>
        </div>
        <div class="ms-3 text-sm font-normal">Gagal menyimpan data pengeluaran.</div>
        <button type="button" class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8 dark:text-gray-500 dark:hover:text-white dark:bg-gray-800 dark:hover:bg-gray-700" onclick="this.parentElement.classList.add('hidden')">
            <span class="sr-only">Close</span>
            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
            </svg>
        </button>
    </div>

    <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
        <svg class="w-6 h-6 text-indigo-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Form Input Pengeluaran
    </h2>

    <form method="POST" enctype="multipart/form-data" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Tanggal Pengeluaran -->
            <div>
                <label for="tanggal" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Pengeluaran</label>
                <input type="date" name="tanggal" id="tanggal" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" required>
            </div>

            <!-- Asal (Diberikan Kepada) -->
            <div>
                <label for="asal" class="block mb-2 text-sm font-medium text-gray-900">Asal (Diberikan Kepada)</label>
                <input type="text" name="asal" id="asal" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" required>
            </div>

            <!-- Nama Unit/Kavling -->
            <div>
                <label for="unit_id" class="block mb-2 text-sm font-medium text-gray-900">Nama Unit/Kavling</label>
                <select name="unit_id" id="unit_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" required>
                    <option value="">-- Pilih Unit --</option>
                    <?php while ($row = $data_unit->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= $row['nama_unit'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Kelompok Akun Pengeluaran -->
            <div>
                <label for="akun_id" class="block mb-2 text-sm font-medium text-gray-900">Kelompok Akun Pengeluaran</label>
                <select name="akun_id" id="akun_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" required>
                    <option value="">-- Pilih Akun --</option>
                    <?php while ($row = $data_akun->fetch_assoc()): ?>
                        <option value="<?= $row['id_akun_pengeluaran'] ?>"><?= $row['akun'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Bank -->
            <div>
                <label for="bank_id" class="block mb-2 text-sm font-medium text-gray-900">Bank</label>
                <select name="bank_id" id="bank_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" required>
                    <option value="">-- Pilih Bank --</option>
                    <?php while ($row = $data_bank->fetch_assoc()): ?>
                        <option value="<?= $row['id_bank'] ?>"><?= $row['bank'] ?> (<?= $row['no_rek'] ?>)</option>
                    <?php endwhile; ?>
                </select>
            </div>

            <!-- Jumlah Pengeluaran -->
            <div>
                <label for="jumlah" class="block mb-2 text-sm font-medium text-gray-900">Jumlah Pengeluaran</label>
                <input type="number" name="jumlah" id="jumlah" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" required>
            </div>

            <!-- No Tanda Terima -->
            <div>
                <label for="no_tanda_terima" class="block mb-2 text-sm font-medium text-gray-900">No Tanda Terima</label>
                <input type="text" name="no_tanda_terima" id="no_tanda_terima" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
            </div>

            <!-- No Faktur -->
            <div>
                <label for="no_faktur" class="block mb-2 text-sm font-medium text-gray-900">No Faktur</label>
                <input type="text" name="no_faktur" id="no_faktur" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5">
            </div>

            <!-- Jenis Pembayaran -->
            <div>
                <label for="jenis_pembayaran" class="block mb-2 text-sm font-medium text-gray-900">Jenis Pembayaran</label>
                <select name="jenis_pembayaran" id="jenis_pembayaran" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2.5" required>
                    <option value="">-- Pilih Jenis --</option>
                    <option value="HUTANG">HUTANG</option>
                    <option value="NON HUTANG">NON HUTANG</option>
                </select>
            </div>

            <!-- Upload File -->
            <div>
                <label for="file_upload" class="block mb-2 text-sm font-medium text-gray-900">Upload File</label>
                <input type="file" name="file_upload" id="file_upload" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                <p class="mt-1 text-sm text-gray-500">Format: PDF, JPG, PNG (Max. 5MB)</p>
            </div>
        </div>

        <!-- Keperluan -->
        <div>
            <label for="keperluan" class="block mb-2 text-sm font-medium text-gray-900">Keperluan</label>
            <textarea name="keperluan" id="keperluan" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-between">
            <!-- Tombol Kembali -->
            <a href="pengeluaran.php" class="inline-flex items-center text-sm font-medium text-white bg-red-500 border border-red-300 hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-200 rounded-lg px-5 py-2.5">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>

            <!-- Tombol Simpan -->
            <button type="submit" class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:outline-none focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                Simpan Pengeluaran
            </button>
        </div>

    </form>
</div>
  </div>

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