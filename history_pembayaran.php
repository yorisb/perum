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
} else {
    header("Location: login.php");
    exit();
}

// Ambil dan hapus pesan sukses dari session
$loginSuccess = '';
if (!empty($_SESSION['success_message'])) {
    $loginSuccess = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Query untuk mendapatkan history pembayaran
$query = "SELECT p.id, k.nama_konsumen, t.no_transaksi, p.tanggal_pembayaran, 
          p.jumlah_pembayaran, p.metode_pembayaran, p.keterangan, p.status_pembayaran
          FROM pembayaran p
          JOIN transaksi t ON p.id_transaksi = t.id
          JOIN konsumen k ON t.id_konsumen = k.id
          WHERE p.status_pembayaran = 'Lunas'
          ORDER BY p.tanggal_pembayaran DESC";
$result = $conn->query($query);
$historyPembayaran = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Unit Perumahan - History Pembayaran</title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js" defer></script>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.tailwindcss.min.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.5/js/dataTables.tailwindcss.min.js"></script>
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

      <!-- Main Content History Pembayaran -->
      <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
          <h2 class="text-2xl font-bold text-gray-800">History Pembayaran</h2>
          <div class="flex space-x-2">
            <button id="printButton" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md flex items-center">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
              </svg>
              Cetak
            </button>
          </div>
        </div>

        <!-- Filter Section -->
        <div class="mb-6 bg-gray-50 p-4 rounded-lg">
          <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
              <input type="date" id="startDate" class="w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
              <input type="date" id="endDate" class="w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nama Konsumen</label>
              <input type="text" id="namaFilter" placeholder="Cari nama..." class="w-full p-2 border border-gray-300 rounded-md">
            </div>
            <div class="flex items-end">
              <button id="filterButton" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md w-full">
                Filter
              </button>
            </div>
          </div>
        </div>

        <!-- Table History Pembayaran -->
        <div class="overflow-x-auto">
          <table id="historyTable" class="w-full table-auto border-collapse">
            <thead>
              <tr class="bg-gray-100 text-gray-700">
                <th class="px-4 py-3 text-left">No</th>
                <th class="px-4 py-3 text-left">Nama Konsumen</th>
                <th class="px-4 py-3 text-left">No Transaksi</th>
                <th class="px-4 py-3 text-left">Tanggal Pembayaran</th>
                <th class="px-4 py-3 text-left">Jumlah</th>
                <th class="px-4 py-3 text-left">Metode</th>
                <th class="px-4 py-3 text-left">Keterangan</th>
                <th class="px-4 py-3 text-left">Status</th>
              </tr>
            </thead>
            <tbody class="text-gray-600">
              <?php foreach ($historyPembayaran as $index => $pembayaran): ?>
                <tr class="border-b border-gray-200 hover:bg-gray-50">
                  <td class="px-4 py-3"><?= $index + 1 ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($pembayaran['nama_konsumen']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($pembayaran['no_transaksi']) ?></td>
                  <td class="px-4 py-3"><?= date('d-m-Y', strtotime($pembayaran['tanggal_pembayaran'])) ?></td>
                  <td class="px-4 py-3">Rp <?= number_format($pembayaran['jumlah_pembayaran'], 0, ',', '.') ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($pembayaran['metode_pembayaran']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($pembayaran['keterangan']) ?></td>
                  <td class="px-4 py-3">
                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                      <?= htmlspecialchars($pembayaran['status_pembayaran']) ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
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

      // Initialize DataTable
      $('#historyTable').DataTable({
        responsive: true,
        language: {
          search: "Cari:",
          lengthMenu: "Tampilkan _MENU_ data per halaman",
          info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
          paginate: {
            first: "Pertama",
            last: "Terakhir",
            next: "Selanjutnya",
            previous: "Sebelumnya"
          }
        }
      });

      // Filter functionality
      $('#filterButton').click(function() {
        const startDate = $('#startDate').val();
        const endDate = $('#endDate').val();
        const nama = $('#namaFilter').val().toLowerCase();
        
        $('#historyTable tbody tr').each(function() {
          const rowDate = $(this).find('td:eq(3)').text().split('-').reverse().join('-');
          const rowNama = $(this).find('td:eq(1)').text().toLowerCase();
          
          let dateMatch = true;
          if (startDate && endDate) {
            dateMatch = (rowDate >= startDate && rowDate <= endDate);
          }
          
          const namaMatch = nama === '' || rowNama.includes(nama);
          
          if (dateMatch && namaMatch) {
            $(this).show();
          } else {
            $(this).hide();
          }
        });
      });

      // Print functionality
      $('#printButton').click(function() {
        window.print();
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

    @media print {
      #sidebar, #navbar, #sidebarToggle, #filterButton, #printButton {
        display: none !important;
      }
      
      #main-content {
        margin-left: 0 !important;
        padding: 0 !important;
      }
      
      body {
        background: white !important;
      }
      
      .bg-white {
        background: white !important;
      }
    }
  </style>
</body>
</html>