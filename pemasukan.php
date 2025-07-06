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

    <section class="py-8 antialiased">
        <div class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <div class="mb-6 flex items-center justify-between gap-4">
                <h2 class="text-xl font-semibold text-gray-900 sm:text-2xl">Kategori Keuangan</h2>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                <!-- Pemasukan -->
                <a href="tampil_pemasukan.php" class="flex flex-col items-center justify-center rounded-lg border border-gray-200 bg-white p-6 hover:bg-indigo-50 hover:border-indigo-200 transition-colors">
                    <div class="mb-3 rounded-full bg-indigo-100 p-4 text-indigo-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Pemasukan</h3>
                    <p class="mt-1 text-center text-sm text-gray-500">Kelola data pemasukan keuangan</p>
                </a>

                <!-- Form Pemasukan -->
                <a href="tambah_pemasukan.php" class="flex flex-col items-center justify-center rounded-lg border border-gray-200 bg-white p-6 hover:bg-green-50 hover:border-green-200 transition-colors">
                    <div class="mb-3 rounded-full bg-green-100 p-4 text-green-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Form Pemasukan</h3>
                    <p class="mt-1 text-center text-sm text-gray-500">Tambah data pemasukan baru</p>
                </a>

                <!-- Cetak Pemasukan -->
                <a href="cetak_pemasukan.php" class="flex flex-col items-center justify-center rounded-lg border border-gray-200 bg-white p-6 hover:bg-blue-50 hover:border-blue-200 transition-colors">
                    <div class="mb-3 rounded-full bg-blue-100 p-4 text-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Cetak Pemasukan</h3>
                    <p class="mt-1 text-center text-sm text-gray-500">Cetak laporan pemasukan</p>
                </a>

                <!-- Pengeluaran -->
                <a href="#" class="flex flex-col items-center justify-center rounded-lg border border-gray-200 bg-white p-6 hover:bg-red-50 hover:border-red-200 transition-colors">
                    <div class="mb-3 rounded-full bg-red-100 p-4 text-red-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Pengeluaran</h3>
                    <p class="mt-1 text-center text-sm text-gray-500">Kelola data pengeluaran</p>
                </a>

                <!-- Laporan Keuangan -->
                <a href="#" class="flex flex-col items-center justify-center rounded-lg border border-gray-200 bg-white p-6 hover:bg-purple-50 hover:border-purple-200 transition-colors">
                    <div class="mb-3 rounded-full bg-purple-100 p-4 text-purple-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Laporan Keuangan</h3>
                    <p class="mt-1 text-center text-sm text-gray-500">Analisis laporan keuangan</p>
                </a>

                <!-- Rekening Bank -->
                <a href="#" class="flex flex-col items-center justify-center rounded-lg border border-gray-200 bg-white p-6 hover:bg-cyan-50 hover:border-cyan-200 transition-colors">
                    <div class="mb-3 rounded-full bg-cyan-100 p-4 text-cyan-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Rekening Bank</h3>
                    <p class="mt-1 text-center text-sm text-gray-500">Kelola rekening bank</p>
                </a>

                <!-- Anggaran -->
                <a href="#" class="flex flex-col items-center justify-center rounded-lg border border-gray-200 bg-white p-6 hover:bg-orange-50 hover:border-orange-200 transition-colors">
                    <div class="mb-3 rounded-full bg-orange-100 p-4 text-orange-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Anggaran</h3>
                    <p class="mt-1 text-center text-sm text-gray-500">Kelola anggaran keuangan</p>
                </a>

                <!-- Transfer Dana -->
                <a href="#" class="flex flex-col items-center justify-center rounded-lg border border-gray-200 bg-white p-6 hover:bg-teal-50 hover:border-teal-200 transition-colors">
                    <div class="mb-3 rounded-full bg-teal-100 p-4 text-teal-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Transfer Dana</h3>
                    <p class="mt-1 text-center text-sm text-gray-500">Transfer antar rekening</p>
                </a>
            </div>
        </div>
    </section>
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