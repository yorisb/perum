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

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Unit Perumahan - Daftar Transaksi</title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
    
    a {
      text-decoration: none !important;
      }

    .card {
      border-radius: 10px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      border: none;
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
    
    .table {
      margin-bottom: 0;
    }
    
    .table thead th {
      border-bottom-width: 1px;
      font-weight: 600;
      color: var(--dark);
      background-color: #f8f9fa;
      white-space: nowrap;
    }
    
    .table-hover tbody tr:hover {
      background-color: rgba(67, 97, 238, 0.05);
    }
    
    .badge {
      font-weight: 500;
      padding: 6px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
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
    
    .btn-sm {
      padding: 5px 12px;
      font-size: 0.8rem;
      border-radius: 6px;
    }
    
    .btn-primary {
      background-color: var(--primary);
      border-color: var(--primary);
    }
    
    .btn-primary:hover {
      background-color: var(--secondary);
      border-color: var(--secondary);
    }
    
    .btn-icon {
      width: 32px;
      height: 32px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0;
      border-radius: 50%;
    }
    
    .currency {
      font-family: 'Courier New', monospace;
      font-weight: 500;
      white-space: nowrap;
    }
    
    .dataTables_length select {
      border-radius: 6px;
      padding: 5px 10px;
      border: 1px solid #dee2e6;
    }
    
    .dataTables_filter input {
      border-radius: 6px;
      padding: 5px 10px;
      border: 1px solid #dee2e6;
      margin-left: 10px;
    }
    

    
    .status-indicator {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      display: inline-block;
      margin-right: 6px;
    }
    
    .status-lunas {
      background-color: var(--success);
    }
    
    .status-dp {
      background-color: var(--warning);
    }
    
    .status-belum {
      background-color: var(--danger);
    }
    
    .action-buttons {
      white-space: nowrap;
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
<div class="px-4 py-6 mt-16">

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <div id="alert-success" class="flex items-center p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-100 dark:bg-green-200 dark:text-green-900" role="alert">
        <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M16.707 5.293a1 1 0 0 0-1.414 0L8 12.586 4.707 9.293a1 1 0 0 0-1.414 1.414l4 4a1 1 0 0 0 1.414 0l8-8a1 1 0 0 0 0-1.414Z"/>
        </svg>
        <span class="sr-only">Success</span>
        <div>
            Transaksi berhasil disimpan!
        </div>
    </div>

    <script>
        // Hilangkan alert setelah 5 detik (5000ms)
        setTimeout(function() {
            const alert = document.getElementById('alert-success');
            if (alert) {
                alert.remove();
            }
        }, 5000);
    </script>
<?php endif; ?>

  <div class="bg-white rounded-xl shadow-md">
    <div class="flex justify-between items-center px-6 py-4 border-b">
      <h5 class="text-xl font-semibold">Daftar Transaksi</h5>
      <div class="flex gap-2">
        <a href="transaksi.php" class="inline-flex items-center px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 rounded-lg text-sm font-medium shadow-sm transition">
          <i class="fas fa-plus mr-2"></i> Tambah Transaksi
        </a>
        <button onclick="window.print()" class="inline-flex items-center px-4 py-2 text-blue-700 border border-blue-600 hover:bg-blue-100 rounded-lg text-sm font-medium transition">
          <i class="fas fa-print mr-2"></i> Cetak
        </button>
      </div>
    </div>

    <div class="overflow-x-auto px-6 py-4">
      <table class="w-full text-sm text-left text-gray-600" id="transaksiTable">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
          <tr>
            <th class="px-4 py-3">No. SPR</th>
            <th class="px-4 py-3">Nama Konsumen</th>
            <th class="px-4 py-3">Kapling/Unit</th>
            <th class="px-4 py-3">Type</th>
            <th class="px-4 py-3">Harga Total</th>
            <th class="px-4 py-3">Uang Muka</th>
            <th class="px-4 py-3">Cicilan</th>
            <th class="px-4 py-3">Cara Bayar</th>
            <th class="px-4 py-3">Tgl Transaksi</th>
            <th class="px-4 py-3">Status</th>
            <th class="px-4 py-3">Aksi</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <?php
          $query = $conn->query("
            SELECT t.*, k.nama_lengkap
            FROM transaksi t
            LEFT JOIN calon_konsumen k ON t.id_konsumen = k.id
            ORDER BY t.tgl_transaksi DESC
          ");
          
          while ($row = $query->fetch_assoc()) {
            $payment_status = calculatePaymentStatus($row);
            $status_class = $payment_status['class'];
            $status_text = $payment_status['text'];
            $status_icon = $payment_status['icon'];

            $tgl_transaksi = date('d/m/Y', strtotime($row['tgl_transaksi']));
            $harga_total = 'Rp ' . number_format($row['total_setelah_penambahan'], 0, ',', '.');
            $uang_muka = 'Rp ' . number_format($row['uang_muka'], 0, ',', '.');
            $cicilan = $row['cicilan'] ? 'Rp ' . number_format($row['cicilan'], 0, ',', '.') : '-';

            echo "
            <tr>
              <td class='px-4 py-3'>{$row['no_transaksi']}</td>
              <td class='px-4 py-3'>{$row['nama_lengkap']}</td>
              <td class='px-4 py-3'>{$row['nama_unit']}</td>
              <td class='px-4 py-3'>{$row['type']}</td>
              <td class='px-4 py-3'>{$harga_total}</td>
              <td class='px-4 py-3'>{$uang_muka}</td>
              <td class='px-4 py-3'>{$cicilan}</td>
              <td class='px-4 py-3'>{$row['cara_pembayaran']}</td>
              <td class='px-4 py-3'>{$tgl_transaksi}</td>
              <td class='px-4 py-3'>
                <span class='inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {$status_class}'>
                  <i class='{$status_icon} mr-1'></i> {$status_text}
                </span>
              </td>
              <td class='px-4 py-3'>
                <div class='flex gap-2'>
                  <a href='detail_transaksi.php?id={$row['id']}' class='text-white bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-lg text-xs font-medium'>
                    <i class='fas fa-eye'></i>
                  </a>
                  <a href='cetak_transaksi.php?id={$row['id']}' target='_blank' class='text-blue-600 border border-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg text-xs font-medium'>
                    <i class='fas fa-print'></i>
                  </a>
                </div>
              </td>
            </tr>";
          }

          function calculatePaymentStatus($transaction) {
            $total_paid = $transaction['uang_muka'];
            $total_due = $transaction['total_setelah_penambahan'];
            
            if ($total_paid >= $total_due) {
              return [
                'class' => 'bg-green-100 text-green-800',
                'text' => 'LUNAS',
                'icon' => 'fas fa-check-circle'
              ];
            } elseif ($total_paid > 0) {
              return [
                'class' => 'bg-yellow-100 text-yellow-800',
                'text' => 'DP',
                'icon' => 'fas fa-hourglass-half'
              ];
            } else {
              return [
                'class' => 'bg-red-100 text-red-800',
                'text' => 'BELUM',
                'icon' => 'fas fa-exclamation-circle'
              ];
            }
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>



<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script>
  $(document).ready(function() {
    $('#transaksiTable').DataTable({
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
      },
      order: [[8, 'desc']], // Default sort by transaction date
      responsive: true,
      dom: '<"top"<"row"<"col-md-6"l><"col-md-6"f>>><"row"<"col-md-12"tr>><"bottom"<"row"<"col-md-6"i><"col-md-6"p>>>',
      pageLength: 25,
      buttons: [
        {
          extend: 'excel',
          text: '<i class="fas fa-file-excel"></i> Excel',
          className: 'btn btn-success btn-sm'
        },
        {
          extend: 'pdf',
          text: '<i class="fas fa-file-pdf"></i> PDF',
          className: 'btn btn-red btn-sm'
        },
        {
          extend: 'print',
          text: '<i class="fas fa-print"></i> Print',
          className: 'btn btn-blue btn-sm'
        }
      ],
      initComplete: function() {
        this.api().buttons().container().appendTo('#transaksiTable_wrapper .col-md-6:eq(0)');
      }
    });
  });
</script>

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