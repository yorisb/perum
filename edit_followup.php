<?php
session_start();
require_once 'koneksi.php';

// Cek login
if (isset($_SESSION['username'])) {
    include 'routes/config.php';
    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Ambil ID follow up
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: daftar_followup.php');
    exit;
}

// Ambil data follow up
$query = "SELECT f.*, c.nama_lengkap, c.no_hp, c.email, c.alamat_lengkap, c.pekerjaan
          FROM jejak_follow_up f
          LEFT JOIN calon_konsumen c ON f.calon_konsumen = c.id
          WHERE f.id = $id";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header('Location: daftar_followup.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Follow Up Konsumen</title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js" defer></script>
  <style>
    body { background-color: #f8f9fa; }
    .card { border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border: none; }
    .card-header { background-color: #3498db; color: white; border-radius: 10px 10px 0 0 !important; font-weight: 600; }
    .btn-primary { background-color: #3498db; border-color: #3498db; }
    .btn-primary:hover { background-color: #2980b9; border-color: #2980b9; }
    .btn-danger { background-color: #e74c3c; border-color: #e74c3c; }
    #sidebar { transition: transform 0.3s ease-out; height: 100vh; overflow-y: auto; overflow-x: hidden; }
    .-translate-x-full { transform: translateX(-100%); }
    .ml-0 { margin-left: 0 !important; }
    .ml-64 { margin-left: 16rem !important; }
    .pl-16 { padding-left: 4rem !important; }
    #main-content, #navbar { transition: margin-left 0.3s ease-out, padding-left 0.3s ease-out; }
  </style>
</head>
<body class="bg-gray-200 min-h-screen flex">

  <!-- Sidebar Toggle Button -->
  <button id="sidebarToggle" class="text-gray-500 bg-white p-2 rounded-md border-2 border-gray-500 fixed top-4 left-4 z-50">
    &#9776;
  </button>

  <!-- Sidebar -->
  <?php include 'templates/sidebar.php'; ?>

  <!-- Main Content -->
  <div id="main-content" class="flex-1 ml-64 p-6 transition-all duration-300 ease-out">

    <!-- Navbar -->
    <div id="navbar" class="rounded-md fixed top-0 left-0 w-full z-10 transition-all duration-300 ease-out ml-64">
      <?php include 'templates/navbar.php'; ?>
    </div>

    <div class="container mx-auto my-5">
      <div class="bg-white p-6 rounded-lg shadow-md mb-4 mt-24 max-w-2xl mx-auto">
        <div class="mb-6">
          <h2 class="text-2xl font-bold text-gray-800 flex items-center">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Edit Follow Up Konsumen
          </h2>
          <p class="text-gray-600 mt-1">Perbarui data follow up konsumen di bawah ini</p>
        </div>

        <form action="proses_edit_followup.php" method="post" class="space-y-6">
          <input type="hidden" name="id" value="<?= $data['id'] ?>">
          <div>
            <label class="block mb-2 text-sm font-medium text-gray-900">Calon Konsumen</label>
            <input type="text" value="<?= htmlspecialchars($data['nama_lengkap']) ?>" class="bg-gray-100 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" readonly>
            <input type="hidden" name="calon_konsumen" value="<?= $data['calon_konsumen'] ?>">
          </div>
          <div class="grid md:grid-cols-2 gap-6">
            <div>
              <label for="tgl_follow_up" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Follow Up <span class="text-red-500">*</span></label>
              <input type="date" id="tgl_follow_up" name="tgl_follow_up" required value="<?= htmlspecialchars($data['tgl_follow_up']) ?>"
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
            </div>
            <div>
              <label for="melalui" class="block mb-2 text-sm font-medium text-gray-900">Melalui <span class="text-red-500">*</span></label>
              <select id="melalui" name="melalui" required
                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5">
                <option value="" disabled>Pilih metode</option>
                <option value="SMS" <?= $data['melalui']=='SMS'?'selected':''; ?>>SMS</option>
                <option value="Telp" <?= $data['melalui']=='Telp'?'selected':''; ?>>Telepon</option>
                <option value="WhatsApp" <?= $data['melalui']=='WhatsApp'?'selected':''; ?>>WhatsApp</option>
                <option value="EMail" <?= $data['melalui']=='EMail'?'selected':''; ?>>Email</option>
                <option value="Tatap Muka" <?= $data['melalui']=='Tatap Muka'?'selected':''; ?>>Tatap Muka</option>
                <option value="Lainnya" <?= $data['melalui']=='Lainnya'?'selected':''; ?>>Lainnya</option>
              </select>
            </div>
          </div>
          <div>
            <label for="telp" class="block mb-2 text-sm font-medium text-gray-900">Nomor Telepon</label>
            <input type="text" id="telp" name="telp" value="<?= htmlspecialchars($data['no_hp']) ?>"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5" readonly>
          </div>
          <div>
            <label for="keterangan" class="block mb-2 text-sm font-medium text-gray-900">Keterangan <span class="text-red-500">*</span></label>
            <textarea id="keterangan" name="keterangan" required rows="4"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"><?= htmlspecialchars($data['keterangan']) ?></textarea>
          </div>
          <div>
            <label for="hasil" class="block mb-2 text-sm font-medium text-gray-900">Hasil Follow Up</label>
            <textarea id="hasil" name="hasil" rows="4"
              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5"><?= htmlspecialchars($data['hasil']) ?></textarea>
          </div>
          <div>
            <label for="status_progres" class="block mb-2 text-sm font-semibold text-gray-800">Status Progres Konsumen <span class="text-red-500">*</span></label>
            <select id="status_progres" name="status_progres" required
              class="block w-full appearance-none p-3 text-sm text-gray-800 border border-gray-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition ease-in-out duration-150">
              <option value="" disabled>Pilih status progres</option>
              <option disabled class="bg-yellow-100 text-yellow-700 font-semibold">-- Tahap Awal --</option>
              <option value="Call In" <?= $data['status_progres']=='Call In'?'selected':''; ?>>Call In</option>
              <option value="Survey" <?= $data['status_progres']=='Survey'?'selected':''; ?>>Survey</option>
              <option value="Reserve" <?= $data['status_progres']=='Reserve'?'selected':''; ?>>Reserve</option>
              <option disabled class="bg-blue-100 text-blue-700 font-semibold">-- Tahap Administrasi --</option>
              <option value="DP" <?= $data['status_progres']=='DP'?'selected':''; ?>>DP</option>
              <option value="Pemberkasan" <?= $data['status_progres']=='Pemberkasan'?'selected':''; ?>>Pemberkasan</option>
              <option value="Wawancara" <?= $data['status_progres']=='Wawancara'?'selected':''; ?>>Wawancara</option>
              <option value="Analisa" <?= $data['status_progres']=='Analisa'?'selected':''; ?>>Analisa</option>
              <option value="SP3K" <?= $data['status_progres']=='SP3K'?'selected':''; ?>>SP3K</option>
              <option disabled class="bg-green-100 text-green-700 font-semibold">-- Tahap Finalisasi --</option>
              <option value="Akad Kredit" <?= $data['status_progres']=='Akad Kredit'?'selected':''; ?>>Akad Kredit</option>
              <option value="Pencairan Akad" <?= $data['status_progres']=='Pencairan Akad'?'selected':''; ?>>Pencairan Akad</option>
              <option value="Cek Fisik Bangunan" <?= $data['status_progres']=='Cek Fisik Bangunan'?'selected':''; ?>>Cek Fisik Bangunan</option>
              <option value="Request Bangun" <?= $data['status_progres']=='Request Bangun'?'selected':''; ?>>Request Bangun</option>
              <option value="Pencairan Topping Off" <?= $data['status_progres']=='Pencairan Topping Off'?'selected':''; ?>>Pencairan Topping Off</option>
              <option value="Pencairan Legalitas" <?= $data['status_progres']=='Pencairan Legalitas'?'selected':''; ?>>Pencairan Legalitas</option>
              <option disabled class="bg-red-100 text-red-700 font-semibold">-- Tahap Khusus --</option>
              <option value="Reject" <?= $data['status_progres']=='Reject'?'selected':''; ?>>Reject</option>
              <option value="Komplain" <?= $data['status_progres']=='Komplain'?'selected':''; ?>>Komplain</option>
            </select>
          </div>
          <div class="flex flex-col sm:flex-row gap-3 mt-8">
            <a href="daftar_followup.php" class="inline-flex items-center justify-center px-5 py-2.5 bg-gray-200 text-gray-800 font-medium rounded-lg hover:bg-gray-300 transition">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
              Kembali ke Daftar Follow Up
            </a>
            <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 w-full sm:w-auto text-center flex items-center justify-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/>
              </svg>
              Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>
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
</body>
</html>