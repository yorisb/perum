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

// Ambil ID transaksi dari parameter URL
$id_transaksi = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query untuk mendapatkan data transaksi
$query = $conn->prepare("
    SELECT t.*, k.nama_lengkap, k.no_hp, k.alamat_lengkap, u.nama_unit, u.type, u.luas_bangunan, u.harga as harga_unit, u.bonus
    FROM transaksi t
    LEFT JOIN calon_konsumen k ON t.id_konsumen = k.id
    LEFT JOIN unit_properti u ON t.nama_unit = u.nama_unit
    WHERE t.id = ?
");
$query->bind_param("i", $id_transaksi);
$query->execute();
$result = $query->get_result();
$transaksi = $result->fetch_assoc();

// Jika transaksi tidak ditemukan
if (!$transaksi) {
    die("Transaksi tidak ditemukan");
}

// Format tanggal
function formatDate($date) {
    return $date ? date('d/m/Y', strtotime($date)) : '-';
}

// Format mata uang
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// Hitung status pembayaran
function getPaymentStatus($transaksi) {
    // Logika sederhana - sesuaikan dengan kebutuhan Anda
    $total_paid = $transaksi['uang_muka']; // Ini harus dihitung dari record pembayaran sebenarnya
    $total_due = $transaksi['total_setelah_penambahan'];
    
    if ($total_paid >= $total_due) {
        return [
            'text' => 'LUNAS',
            'class' => 'success',
            'icon' => 'check-circle'
        ];
    } elseif ($total_paid > 0) {
        return [
            'text' => 'DP',
            'class' => 'warning',
            'icon' => 'hourglass-half'
        ];
    } else {
        return [
            'text' => 'BELUM BAYAR',
            'class' => 'danger',
            'icon' => 'exclamation-circle'
        ];
    }
}

$payment_status = getPaymentStatus($transaksi);
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
  <style>
    :root {
      --primary: #4361ee;
      --primary-light: #e6e9ff;
      --secondary: #3f37c9;
      --success: #4cc9f0;
      --danger: #f72585;
      --warning: #f8961e;
      --light: #f8f9fa;
      --dark: #212529;
      --gray: #6c757d;
    }
    
    
    .card {
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      border: none;
      margin-bottom: 20px;
    }
    
    .card-header {
      background-color: white;
      border-bottom: 1px solid rgba(0, 0, 0, 0.08);
      padding: 20px;
      border-radius: 10px 10px 0 0 !important;
    }
    
    .card-title {
      color: var(--primary);
      font-weight: 600;
      margin: 0;
    }
    
    .badge {
      font-weight: 500;
      padding: 8px 12px;
      border-radius: 20px;
      font-size: 0.9rem;
    }
    
    .badge-success {
      background-color: rgba(76, 201, 240, 0.1);
      color: #4cc9f0;
    }
    
    .badge-warning {
      background-color: rgba(248, 150, 30, 0.1);
      color: #f8961e;
    }
    
    .badge-danger {
      background-color: rgba(247, 37, 133, 0.1);
      color: #f72585;
    }
    
    .detail-label {
      font-weight: 500;
      color: var(--gray);
      margin-bottom: 5px;
    }
    
    .detail-value {
      font-weight: 500;
      margin-bottom: 15px;
    }
    
    .currency {
      font-family: 'Courier New', monospace;
      font-weight: 500;
    }
    
    .status-indicator {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 6px;
    }
    
    .status-success {
      background-color: var(--success);
    }
    
    .status-warning {
      background-color: var(--warning);
    }
    
    .status-danger {
      background-color: var(--danger);
    }
    
    .section-title {
      color: var(--primary);
      font-weight: 600;
      margin-bottom: 15px;
      padding-bottom: 8px;
      border-bottom: 1px solid var(--primary-light);
    }
    
    .btn-print {
      background-color: white;
      border: 1px solid var(--primary);
      color: var(--primary);
    }
    
    .btn-print:hover {
      background-color: var(--primary-light);
    }
    
    .payment-plan {
      border-left: 3px solid var(--primary);
      padding-left: 15px;
      margin-bottom: 20px;
    }
    
    .payment-item {
      margin-bottom: 10px;
    }
    
    @media print {
      .no-print {
        display: none;
      }
      
      
      .card {
        box-shadow: none;
        border: 1px solid #ddd;
      }
    }
  </style>
  <style>
    @media print {
      /* Sembunyikan elemen-elemen tertentu saat print */
      .no-print {
        display: none !important;
      }
    }
</style>
<style>
  @media print {
    /* Atur margin body saat print */
    body {
      margin: 0 !important;
      padding: 0 !important;
    }

    /* Hapus jarak atas dari container utama */
    .main-content {
      margin-top: 0 !important;
      padding-top: 0 !important;
    }

    /* Sembunyikan elemen-elemen fixed yang masih makan ruang */
    .no-print, header, nav, .navbar, .sidebar {
      display: none !important;
    }
  }
</style>

</head>
<body class="bg-gray-100 min-h-screen flex">

  <!-- Sidebar Toggle Button -->
  <button id="sidebarToggle" class="no-print text-gray-500 bg-white p-2 rounded-md border-2 border-gray-500 fixed top-4 left-4 z-50">
    &#9776; <!-- Hamburger Icon -->
  </button>

  <!-- Sidebar -->
  <div class="no-print">
    <?php include 'templates/sidebar.php'; ?>
  </div>
  
  <!-- Main Content -->
  <div id="main-content" class="flex-1 ml-64 p-6 transition-all duration-300 ease-out">

    <!-- Navbar -->
    <div id="navbar" class="rounded-md fixed top-0 left-0 w-full z-10 transition-all duration-300 ease-out ml-64">
      <?php include 'templates/navbar.php'; ?>
    </div>

    <!-- Content -->
<div class="container mx-auto py-4 px-4 sm:px-6 lg:px-8 mt-28">
  <!-- Header dengan Tombol -->
  <div class="flex justify-between items-center mb-6 no-print">
    <h2 class="text-2xl font-bold text-gray-800">Detail Transaksi</h2>
    <div class="flex space-x-2">
      <a href="daftar_transaksi.php" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg class="-ml-1 mr-2 h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Kembali
      </a>
      <button onclick="window.print()" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
        </svg>
        Cetak
      </button>
    </div>
  </div>

  <!-- Header Transaksi -->
  <div class="bg-white shadow rounded-lg mb-6 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
      <div class="flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Transaksi #<?= htmlspecialchars($transaksi['no_transaksi']) ?></h3>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium <?= $payment_status['class'] === 'success' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
          <svg class="-ml-1 mr-1.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?= $payment_status['icon'] === 'check-circle' ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' ?>"></path>
          </svg>
          <?= $payment_status['text'] ?>
        </span>
      </div>
    </div>
    <div class="px-6 py-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <div class="text-sm font-medium text-gray-500">Tanggal Transaksi</div>
          <div class="mt-1 text-sm text-gray-900"><?= formatDate($transaksi['tgl_transaksi']) ?></div>
          
          <div class="mt-4 text-sm font-medium text-gray-500">Konsumen</div>
          <div class="mt-1 text-sm text-gray-900">
            <?= htmlspecialchars($transaksi['nama_lengkap']) ?><br>
            <span class="text-gray-500"><?= htmlspecialchars($transaksi['no_hp']) ?></span>
          </div>
        </div>
        <div>
          <div class="text-sm font-medium text-gray-500">Unit/Kapling</div>
          <div class="mt-1 text-sm text-gray-900">
            <?= htmlspecialchars($transaksi['nama_unit']) ?> (<?= htmlspecialchars($transaksi['type']) ?>)<br>
            <span class="text-gray-500">Luas Bangunan: <?= $transaksi['luas_bangunan'] ?> m²</span>
          </div>
          
          <div class="mt-4 text-sm font-medium text-gray-500">Kode Konsumen</div>
          <div class="mt-1 text-sm text-gray-900"><?= htmlspecialchars($transaksi['kode_konsumen']) ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Informasi Harga -->
    <div class="lg:col-span-2">
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Rincian Harga</h3>
        </div>
        <div class="px-6 py-4">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h4 class="text-md font-medium text-gray-700 mb-3">Harga Unit</h4>
              <div class="space-y-4">
                <div class="flex justify-between">
                  <span class="text-sm text-gray-600">Harga Unit</span>
                  <span class="text-sm font-medium text-gray-900"><?= formatCurrency($transaksi['harga_unit']) ?></span>
                </div>
                
                <div class="flex justify-between">
                  <span class="text-sm text-gray-600">Bonus/Keterangan</span>
                  <span class="text-sm text-gray-900"><?= $transaksi['bonus'] ? htmlspecialchars($transaksi['bonus']) : '-' ?></span>
                </div>
              </div>
            </div>
            
            <div>
              <h4 class="text-md font-medium text-gray-700 mb-3">Penambahan</h4>
              <div class="space-y-4">
                <div class="flex justify-between">
                  <span class="text-sm text-gray-600">Harga Kesepakatan</span>
                  <span class="text-sm font-medium text-gray-900"><?= formatCurrency($transaksi['harga_kesepakatan']) ?></span>
                </div>
                
                <div class="flex justify-between">
                  <span class="text-sm text-gray-600">Luas Tanah</span>
                  <span class="text-sm text-gray-900"><?= $transaksi['luas_tanah'] ?> m² × <?= formatCurrency($transaksi['harga_per_m2']) ?> = <?= formatCurrency($transaksi['total_harga_penambahan']) ?></span>
                </div>
                
                <?php if ($transaksi['penambahan_lain']): ?>
                <div class="flex justify-between">
                  <span class="text-sm text-gray-600">Penambahan Lain</span>
                  <span class="text-sm text-gray-900"><?= htmlspecialchars($transaksi['penambahan_lain']) ?> (<?= formatCurrency($transaksi['total_harga_penambahan_lain']) ?>)</span>
                </div>
                <?php endif; ?>
                
                <div class="flex justify-between pt-2 border-t border-gray-200">
                  <span class="text-sm font-medium text-gray-600">Total Penambahan</span>
                  <span class="text-sm font-medium text-gray-900"><?= formatCurrency($transaksi['total_penambahan']) ?></span>
                </div>
                
                <div class="flex justify-between bg-gray-50 p-2 rounded">
                  <span class="text-sm font-bold text-gray-700">Total Setelah Penambahan</span>
                  <span class="text-sm font-bold text-gray-900"><?= formatCurrency($transaksi['total_setelah_penambahan']) ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Sidebar Kanan -->
    <div class="space-y-6">
      <!-- Rencana Pembayaran -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Rencana Pembayaran</h3>
        </div>
        <div class="px-6 py-4">
          <div class="space-y-4">
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Tanda Jadi</span>
              <div class="text-right">
                <span class="text-sm font-medium text-gray-900"><?= formatCurrency($transaksi['tanda_jadi']) ?></span>
                <p class="text-xs text-gray-500"><?= $transaksi['status_tanda_jadi'] == 'masuk' ? 'Masuk harga jual' : 'Tidak masuk harga jual' ?></p>
              </div>
            </div>
            
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Uang Muka</span>
              <span class="text-sm font-medium text-gray-900"><?= formatCurrency($transaksi['uang_muka']) ?></span>
            </div>
            
            <div class="flex justify-between pt-2 border-t border-gray-200">
              <span class="text-sm font-medium text-gray-600">Total Akhir</span>
              <span class="text-sm font-bold text-gray-900"><?= formatCurrency($transaksi['total_akhir']) ?></span>
            </div>
            
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Cara Pembayaran</span>
              <span class="text-sm text-gray-900"><?= $transaksi['cara_pembayaran'] ? htmlspecialchars($transaksi['cara_pembayaran']) : '-' ?></span>
            </div>
            
            <?php if ($transaksi['periode_cicilan']): ?>
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Cicilan</span>
              <div class="text-right">
                <span class="text-sm text-gray-900"><?= formatCurrency($transaksi['cicilan']) ?> / bulan</span>
                <p class="text-xs text-gray-500"><?= $transaksi['periode_cicilan'] ?> bulan</p>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
      
      <!-- Jadwal Pembayaran -->
      <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Jadwal Pembayaran</h3>
        </div>
        <div class="px-6 py-4">
          <div class="space-y-4">
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Tanda Jadi</span>
              <span class="text-sm text-gray-900"><?= formatDate($transaksi['rencana_tgl_bayar_tanda_jadi']) ?></span>
            </div>
            
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Uang Muka</span>
              <span class="text-sm text-gray-900"><?= formatDate($transaksi['rencana_tgl_bayar_uang_muka']) ?></span>
            </div>
            
            <div class="flex justify-between">
              <span class="text-sm text-gray-600">Pembayaran</span>
              <span class="text-sm text-gray-900"><?= formatDate($transaksi['rencana_tgl_bayar_pembayaran']) ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
  <!-- Angsuran Uang Muka -->
  <div class="bg-white shadow rounded-lg overflow-hidden mt-6 print:mt-0">
  <div class="px-4 py-2 border-b border-gray-200">
    <h3 class="text-base font-semibold text-gray-900">Rincian Angsuran Uang Muka</h3>
  </div>
  <div class="px-4 py-2">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-xs">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">No</th>
            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Tanggal Jatuh Tempo</th>
            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th class="px-3 py-2 text-left font-medium text-gray-500 uppercase tracking-wider">Tanggal Pembayaran</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php
          $angsuran = [];
          for ($i = 1; $i <= $transaksi['periode_uang_muka']; $i++) {
            $angsuran[] = [
              'no' => $i,
              'jumlah' => $transaksi['angsuran_'.$i] ?? 0,
              'jatuh_tempo' => date('Y-m-d', strtotime($transaksi['tgl_transaksi'] . " + $i month")),
              'status' => 'Belum Lunas',
              'tgl_bayar' => null
            ];
          }

          foreach ($angsuran as $item): 
          ?>
          <tr>
            <td class="px-3 py-2 text-sm text-gray-500"><?= $item['no'] ?></td>
            <td class="px-3 py-2 text-sm text-gray-900"><?= formatCurrency($item['jumlah']) ?></td>
            <td class="px-3 py-2 text-sm text-gray-500"><?= formatDate($item['jatuh_tempo']) ?></td>
            <td class="px-3 py-2">
              <span class="px-2 inline-flex text-xs font-medium rounded-full bg-red-100 text-red-800">
                <svg class="-ml-0.5 mr-1 h-3 w-3 text-red-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <?= $item['status'] ?>
              </span>
            </td>
            <td class="px-3 py-2 text-sm text-gray-500"><?= $item['tgl_bayar'] ? formatDate($item['tgl_bayar']) : '-' ?></td>
          </tr>
          <?php endforeach; ?>
          <tr class="bg-gray-50">
            <td class="px-3 py-2 text-sm font-medium text-gray-900">Total</td>
            <td class="px-3 py-2 text-sm font-medium text-gray-900"><?= formatCurrency($transaksi['uang_muka']) ?></td>
            <td colspan="3"></td>
          </tr>
        </tbody>
      </table>
    </div>
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

<script>
  // Fungsi untuk mencetak
  function printDocument() {
    window.print();
  }
</script>

</body>

</html>