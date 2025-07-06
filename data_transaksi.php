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

$query = "SELECT 
    no_transaksi, 
    tgl_transaksi, 
    id_konsumen, 
    harga_kesepakatan, 
    kode_konsumen, cara_pem
    luas_tanah, 
    harga_per_m2, 
    total_harga_penambahan, 
    penambahan_lain, 
    total_harga_penambahan_lain, 
    total_penambahan, 
    total_setelah_penambahan, 
    nama_unit, 
    type, 
    luas_bangunan, 
    tanda_jadi, 
    status_tanda_jadi, 
    periode_uang_muka, 
    harga, 
    uang_muka, 
    total_akhir, 
    cara_pembayaran, 
    periode_cicilan, 
    cicilan, 
    rencana_tgl_bayar_tanda_jadi, 
    rencana_tgl_bayar_uang_muka, 
    rencana_tgl_bayar_pembayaran
  FROM transaksi
  ORDER BY tgl_transaksi DESC";
$result = mysqli_query($conn, $query);
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
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.tailwindcss.min.css">
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

    <main class="flex-1 bg-gray-100">
      <div class="max-w-8xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
          <h1 class="text-3xl font-bold text-gray-800 mb-2">Data Transaksi</h1>
          <p class="text-gray-500">Daftar seluruh transaksi unit perumahan.</p>
        </div>
        
        <div class="bg-white rounded-xl shadow overflow-hidden">
          <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
            <div class="flex items-center space-x-2">
              <span class="text-gray-600">Show</span>
              <select class="border border-gray-300 rounded-md px-2 py-1 text-sm">
                <option>10</option>
                <option>25</option>
                <option>50</option>
                <option>100</option>
              </select>
              <span class="text-gray-600">entries</span>
            </div>
            <div class="relative">
              <input type="text" placeholder="Search..." class="border border-gray-300 rounded-md px-3 py-1 pl-8 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
              <svg class="w-4 h-4 absolute left-2.5 top-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
              </svg>
            </div>
          </div>
          
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="transaksiTable">
              <thead class="bg-gray-50">
                <tr>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Transaksi</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Transaksi</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Konsumen</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pembayaran</th>
                  <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <?php if ($result && mysqli_num_rows($result) > 0): $no = 1; ?>
                  <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr class="hover:bg-gray-50 transition">
                      <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $no++; ?></td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['no_transaksi']); ?></div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><?= htmlspecialchars($row['tgl_transaksi']); ?></div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><?= htmlspecialchars($row['id_konsumen']); ?></div>
                        <div class="text-sm text-gray-500"><?= htmlspecialchars($row['kode_konsumen']); ?></div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-gray-900"><?= htmlspecialchars($row['nama_unit']); ?></div>
                        <div class="text-sm text-gray-500"><?= htmlspecialchars($row['type']); ?></div>
                        <div class="text-xs text-gray-400">LT: <?= htmlspecialchars($row['luas_tanah']); ?>m² | LB: <?= htmlspecialchars($row['luas_bangunan']); ?>m²</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 font-semibold">Rp <?= number_format($row['harga_kesepakatan'], 0, ',', '.'); ?></div>
                        <div class="text-xs text-gray-500">Rp <?= number_format($row['harga_per_m2'], 0, ',', '.'); ?>/m²</div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900"><?= htmlspecialchars($row['cara_pembayaran']); ?></div>
                        <div class="text-xs text-gray-500"><?= htmlspecialchars($row['periode_cicilan']); ?>x</div>
                        <div class="text-xs font-medium <?= $row['status_tanda_jadi'] == 'Lunas' ? 'text-green-600' : 'text-yellow-600' ?>">
                          <?= htmlspecialchars($row['status_tanda_jadi']); ?>
                        </div>
                      </td>
                      <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button class="text-blue-600 hover:text-blue-900 mr-3">Detail</button>
                        <button class="text-green-600 hover:text-green-900">Edit</button>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-gray-500">Tidak ada data transaksi.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          
          <div class="px-4 py-3 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
            <div class="text-sm text-gray-700">
              Showing <span class="font-medium">1</span> to <span class="font-medium">10</span> of <span class="font-medium"><?= mysqli_num_rows($result); ?></span> results
            </div>
            <div class="flex space-x-2">
              <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Previous</button>
              <button class="px-3 py-1 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Next</button>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <!-- JavaScript Libraries -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>

  <script>
    $(document).ready(function() {
      $('#transaksiTable').DataTable({
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        language: {
          search: "_INPUT_",
          searchPlaceholder: "Search...",
        }
      });
    });

    // Sidebar Toggle
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

    /* DataTables customization */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
      padding: 0.25rem 0.75rem;
      border: 1px solid #d1d5db;
      margin-left: -1px;
      color: #374151;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
      background: #3b82f6;
      color: white !important;
      border-color: #3b82f6;
    }
    
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
      background: #e5e7eb;
      color: #111827 !important;
    }
  </style>
</body>
</html>