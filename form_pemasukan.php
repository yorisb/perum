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
$data_akun = $conn->query("SELECT id_akun_pemasukan, nama_kelompok FROM akun_pemasukan");
$data_bank = $conn->query("SELECT id_bank, bank, no_rek FROM akun_bank");

// Proses form
$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tanggal = $_POST['tanggal'] ?? '';
    $asal = $_POST['asal'] ?? '';
    $unit_id = $_POST['unit_id'] ?? '';
    $akun_id = $_POST['akun_id'] ?? '';
    $bank_id = $_POST['bank_id'] ?? '';
    $keterangan = $_POST['keterangan'] ?? '';
    $jumlah = $_POST['jumlah'] ?? '';
    $no_tanda_terima = $_POST['no_tanda_terima'] ?? '';
    $file_upload = '';

    // Validasi
    if (empty($tanggal)) $errors['tanggal'] = 'Tanggal wajib diisi';
    if (empty($asal)) $errors['asal'] = 'Asal pemasukan wajib diisi';
    if (empty($unit_id)) $errors['unit_id'] = 'Unit wajib dipilih';
    if (empty($akun_id)) $errors['akun_id'] = 'Kelompok pemasukan wajib dipilih';
    if (empty($bank_id)) $errors['bank_id'] = 'Bank wajib dipilih';
    if (empty($jumlah)) $errors['jumlah'] = 'Jumlah wajib diisi';

    if (empty($errors)) {
        // Handle upload file
        if ($_FILES['file_upload']['name']) {
            $target_dir = "pemasukan/uploads/";
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $file_upload = $target_dir . basename($_FILES["file_upload"]["name"]);
            move_uploaded_file($_FILES["file_upload"]["tmp_name"], $file_upload);
        }

        // Simpan ke tabel pemasukan
        $stmt = $conn->prepare("INSERT INTO pemasukan (tanggal, asal, unit_id, akun_id, bank_id, keterangan, jumlah, no_tanda_terima, file_upload) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiiissds", $tanggal, $asal, $unit_id, $akun_id, $bank_id, $keterangan, $jumlah, $no_tanda_terima, $file_upload);

        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Data pemasukan berhasil disimpan!';
            header("Location: tambah_pemasukan.php");
            exit();
        } else {
            $errors['database'] = "Gagal menyimpan data: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tambah Pemasukan - Dashboard</title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js" defer></script>
  <style>
    .form-container {
      max-width: 800px;
      margin: 0 auto;
    }
    .form-label {
      font-weight: 500;
      color: #4b5563;
      margin-bottom: 0.5rem;
    }
    .form-input {
      border: 1px solid #d1d5db;
      border-radius: 0.375rem;
      padding: 0.5rem 0.75rem;
      width: 100%;
    }
    .form-input:focus {
      border-color: #3b82f6;
      box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .error-message {
      color: #ef4444;
      font-size: 0.875rem;
      margin-top: 0.25rem;
    }
    .btn-submit {
      background-color: #3b82f6;
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 0.375rem;
      font-weight: 500;
    }
    .btn-submit:hover {
      background-color: #2563eb;
    }
    .file-input-label {
      display: flex;
      align-items: center;
      padding: 0.5rem 0.75rem;
      border: 1px dashed #d1d5db;
      border-radius: 0.375rem;
      cursor: pointer;
    }
    .file-input-label:hover {
      border-color: #3b82f6;
    }
  </style>
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
  <div id="navbar" class="rounded-md fixed top-0 left-0 w-full z-10 transition-all duration-300 ease-out ml-64">
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
          <p class="font-semibold">Sukses!</p>
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

    <div class="max-w-4xl mx-auto">
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-800">
            <i class="fas fa-money-bill-wave mr-2 text-blue-600"></i>Tambah Pemasukan
          </h2>
          <a href="tampil_pemasukan.php" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
          </a>
        </div>
        
        <?php if (!empty($errors['database'])): ?>
          <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
            <?= htmlspecialchars($errors['database']); ?>
          </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="tanggal" class="block mb-2 text-sm font-medium text-gray-900">Tanggal Pemasukan</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <i class="fas fa-calendar text-gray-500"></i>
                </div>
                <input type="date" id="tanggal" name="tanggal" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 <?= !empty($errors['tanggal']) ? 'border-red-500' : '' ?>" value="<?= htmlspecialchars($_POST['tanggal'] ?? '') ?>" required>
              </div>
              <?php if (!empty($errors['tanggal'])): ?>
                <p class="mt-2 text-sm text-red-600"><?= htmlspecialchars($errors['tanggal']) ?></p>
              <?php endif; ?>
            </div>

            <div>
              <label for="asal" class="block mb-2 text-sm font-medium text-gray-900">Terima Dari (Asal)</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <i class="fas fa-user text-gray-500"></i>
                </div>
                <input type="text" id="asal" name="asal" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 <?= !empty($errors['asal']) ? 'border-red-500' : '' ?>" value="<?= htmlspecialchars($_POST['asal'] ?? '') ?>" required>
              </div>
              <?php if (!empty($errors['asal'])): ?>
                <p class="mt-2 text-sm text-red-600"><?= htmlspecialchars($errors['asal']) ?></p>
              <?php endif; ?>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
              <label for="unit_id" class="block mb-2 text-sm font-medium text-gray-900">Nama Unit/Kavling</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <i class="fas fa-home text-gray-500"></i>
                </div>
                <select id="unit_id" name="unit_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 <?= !empty($errors['unit_id']) ? 'border-red-500' : '' ?>" required>
                  <option value="">-- Pilih Unit --</option>
                  <?php while ($row = $data_unit->fetch_assoc()): ?>
                    <option value="<?= $row['id'] ?>" <?= (($_POST['unit_id'] ?? '') == $row['id'] ? 'selected' : '') ?>>
                      <?= htmlspecialchars($row['nama_unit']) ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <?php if (!empty($errors['unit_id'])): ?>
                <p class="mt-2 text-sm text-red-600"><?= htmlspecialchars($errors['unit_id']) ?></p>
              <?php endif; ?>
            </div>

            <div>
              <label for="akun_id" class="block mb-2 text-sm font-medium text-gray-900">Kelompok Pemasukan</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <i class="fas fa-list text-gray-500"></i>
                </div>
                <select id="akun_id" name="akun_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 <?= !empty($errors['akun_id']) ? 'border-red-500' : '' ?>" required>
                  <option value="">-- Pilih Kelompok --</option>
                  <?php while ($row = $data_akun->fetch_assoc()): ?>
                    <option value="<?= $row['id_akun_pemasukan'] ?>" <?= (($_POST['akun_id'] ?? '') == $row['id_akun_pemasukan'] ? 'selected' : '') ?>>
                      <?= htmlspecialchars($row['nama_kelompok']) ?>
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <?php if (!empty($errors['akun_id'])): ?>
                <p class="mt-2 text-sm text-red-600"><?= htmlspecialchars($errors['akun_id']) ?></p>
              <?php endif; ?>
            </div>

            <div>
              <label for="bank_id" class="block mb-2 text-sm font-medium text-gray-900">Bank</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <i class="fas fa-university text-gray-500"></i>
                </div>
                <select id="bank_id" name="bank_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 <?= !empty($errors['bank_id']) ? 'border-red-500' : '' ?>" required>
                  <option value="">-- Pilih Bank --</option>
                  <?php while ($row = $data_bank->fetch_assoc()): ?>
                    <option value="<?= $row['id_bank'] ?>" <?= (($_POST['bank_id'] ?? '') == $row['id_bank'] ? 'selected' : '') ?>>
                      <?= htmlspecialchars($row['bank']) ?> (<?= htmlspecialchars($row['no_rek']) ?>)
                    </option>
                  <?php endwhile; ?>
                </select>
              </div>
              <?php if (!empty($errors['bank_id'])): ?>
                <p class="mt-2 text-sm text-red-600"><?= htmlspecialchars($errors['bank_id']) ?></p>
              <?php endif; ?>
            </div>
          </div>

          <div>
            <label for="keterangan" class="block mb-2 text-sm font-medium text-gray-900">Keterangan</label>
            <textarea id="keterangan" name="keterangan" rows="3" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="jumlah" class="block mb-2 text-sm font-medium text-gray-900">Jumlah Pemasukan</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <span class="text-gray-500">Rp</span>
                </div>
                <input type="number" id="jumlah" name="jumlah" step="0.01" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 <?= !empty($errors['jumlah']) ? 'border-red-500' : '' ?>" value="<?= htmlspecialchars($_POST['jumlah'] ?? '') ?>" required>
              </div>
              <?php if (!empty($errors['jumlah'])): ?>
                <p class="mt-2 text-sm text-red-600"><?= htmlspecialchars($errors['jumlah']) ?></p>
              <?php endif; ?>
            </div>

            <div>
              <label for="no_tanda_terima" class="block mb-2 text-sm font-medium text-gray-900">No Tanda Terima</label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                  <i class="fas fa-receipt text-gray-500"></i>
                </div>
                <input type="text" id="no_tanda_terima" name="no_tanda_terima" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" value="<?= htmlspecialchars($_POST['no_tanda_terima'] ?? '') ?>">
              </div>
            </div>
          </div>

          <div>
            <label class="block mb-2 text-sm font-medium text-gray-900" for="file_upload">Upload File</label>
            <div class="flex items-center justify-center w-full">
              <label for="file_upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                  <i class="fas fa-cloud-upload-alt text-2xl text-gray-500 mb-2"></i>
                  <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk upload</span> atau drag & drop</p>
                  <p class="text-xs text-gray-500" id="file-name">PDF, JPG, PNG (MAX. 5MB)</p>
                </div>
                <input id="file_upload" name="file_upload" type="file" class="hidden" onchange="document.getElementById('file-name').textContent = this.files[0] ? this.files[0].name : 'PDF, JPG, PNG (MAX. 5MB)'" />
              </label>
            </div>
          </div>

          <div class="flex justify-end pt-6">
            <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
              <i class="fas fa-save mr-2"></i> Simpan Pemasukan
            </button>
          </div>
        </form>
      </div>
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