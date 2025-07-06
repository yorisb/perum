<?php
session_start();

// Cek apakah user sudah login, jika tidak redirect ke login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

include 'routes/config.php';

// Proses form submission
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validasi input
    $nama_unit = trim($_POST['nama_unit'] ?? '');
    $type = trim($_POST['type'] ?? '');
    $luas_bangunan = trim($_POST['luas_bangunan'] ?? '');
    $harga = trim($_POST['harga'] ?? '');
    $bonus = trim($_POST['bonus'] ?? '');
    $status = '0'; // Status di-set otomatis ke '0'

    if (empty($nama_unit)) {
        $errors['nama_unit'] = 'Nama unit wajib diisi';
    }

    if (empty($type)) {
        $errors['type'] = 'Tipe unit wajib diisi';
    }

    if (empty($luas_bangunan)) {
        $errors['luas_bangunan'] = 'Luas bangunan wajib diisi';
    } elseif (!is_numeric($luas_bangunan)) {
        $errors['luas_bangunan'] = 'Luas bangunan harus berupa angka';
    }

    if (empty($harga)) {
        $errors['harga'] = 'Harga wajib diisi';
    } elseif (!is_numeric($harga)) {
        $errors['harga'] = 'Harga harus berupa angka';
    }

    if (empty($errors)) {
        try {
            $stmt = $conn->prepare("INSERT INTO unit_properti (nama_unit, type, luas_bangunan, harga, bonus, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('ssddss', $nama_unit, $type, $luas_bangunan, $harga, $bonus, $status);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Unit properti berhasil ditambahkan!';
                header("Location: unit.php");
                exit();
            } else {
                $errors['database'] = 'Gagal menambahkan unit: ' . $stmt->error;
            }
        } catch (Exception $e) {
            $errors['database'] = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}

// Ambil dan hapus pesan sukses dari session
$loginSuccess = '';
if (!empty($_SESSION['success_message'])) {
    $loginSuccess = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
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

      <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Tambah Unit Properti</h2>
        
        <?php if (!empty($errors['database'])): ?>
          <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?= htmlspecialchars($errors['database']); ?>
          </div>
        <?php endif; ?>

        <form action="unit.php" method="POST" class="space-y-4">
          <div>
            <label for="nama_unit" class="block text-sm font-medium text-gray-700">Nama Unit</label>
            <input type="text" id="nama_unit" name="nama_unit" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border <?= !empty($errors['nama_unit']) ? 'border-red-500' : '' ?>" 
                   value="<?= htmlspecialchars($_POST['nama_unit'] ?? '') ?>">
            <?php if (!empty($errors['nama_unit'])): ?>
              <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['nama_unit']) ?></p>
            <?php endif; ?>
          </div>

          <div>
            <label for="type" class="block text-sm font-medium text-gray-700">Tipe Unit</label>
            <input type="text" id="type" name="type" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border <?= !empty($errors['type']) ? 'border-red-500' : '' ?>" 
                   value="<?= htmlspecialchars($_POST['type'] ?? '') ?>">
            <?php if (!empty($errors['type'])): ?>
              <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['type']) ?></p>
            <?php endif; ?>
          </div>

          <div>
            <label for="luas_bangunan" class="block text-sm font-medium text-gray-700">Luas Bangunan (m²)</label>
            <input type="number" step="0.01" id="luas_bangunan" name="luas_bangunan" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border <?= !empty($errors['luas_bangunan']) ? 'border-red-500' : '' ?>" 
                   value="<?= htmlspecialchars($_POST['luas_bangunan'] ?? '') ?>">
            <?php if (!empty($errors['luas_bangunan'])): ?>
              <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['luas_bangunan']) ?></p>
            <?php endif; ?>
          </div>

          <div>
            <label for="harga" class="block text-sm font-medium text-gray-700">Harga (Rp)</label>
            <input type="number" step="0.01" id="harga" name="harga" 
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border <?= !empty($errors['harga']) ? 'border-red-500' : '' ?>" 
                   value="<?= htmlspecialchars($_POST['harga'] ?? '') ?>">
            <?php if (!empty($errors['harga'])): ?>
              <p class="mt-1 text-sm text-red-600"><?= htmlspecialchars($errors['harga']) ?></p>
            <?php endif; ?>
          </div>

          <div>
            <label for="bonus" class="block text-sm font-medium text-gray-700">Bonus</label>
            <textarea id="bonus" name="bonus" rows="3" 
                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2 border"><?= htmlspecialchars($_POST['bonus'] ?? '') ?></textarea>
          </div>

          <div class="flex justify-end space-x-4">
            <button type="reset" class="px-4 py-2 bg-gray-300 text-gray-800 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500">
              Reset
            </button>
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
              Simpan
            </button>
          </div>
        </form>
      </div>
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
      height: 100vh;
      overflow-y: auto;
      overflow-x: hidden;
    }

    .-translate-x-full {
      transform: translateX(-100%);
    }

    .ml-0 {
      margin-left: 0 !important;
    }

    .ml-64 {
      margin-left: 16rem !important;
    }

    .pl-16 {
      padding-left: 4rem !important;
    }

    #main-content, #navbar {
      transition: margin-left 0.3s ease-out, padding-left 0.3s ease-out;
    }
  </style>
</body>
</html>