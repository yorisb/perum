<?php
session_start();
require_once 'koneksi.php';

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

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Ambil parameter filter dari URL
$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';
$status = isset($_GET['status']) ? mysqli_real_escape_string($koneksi, $_GET['status']) : '';
$date = isset($_GET['date']) ? mysqli_real_escape_string($koneksi, $_GET['date']) : '';

// Query dengan JOIN ke tabel calon_konsumen dan filter
$query = "SELECT f.*, 
                 c.nama_lengkap AS nama_konsumen,
                 c.no_hp,
                 c.email,
                 c.alamat_lengkap,
                 c.pekerjaan
          FROM jejak_follow_up f
          LEFT JOIN calon_konsumen c ON f.calon_konsumen = c.id
          WHERE 1=1";

// Tambahkan filter pencarian
if (!empty($search)) {
    $query .= " AND (c.nama_lengkap LIKE '%$search%' OR c.no_hp LIKE '%$search%' OR f.keterangan LIKE '%$search%')";
}

// Tambahkan filter status
if (!empty($status)) {
    $query .= " AND f.status_progres = '$status'";
}

// Tambahkan filter tanggal
if (!empty($date)) {
    $query .= " AND DATE(f.tgl_follow_up) = '$date'";
}

// Urutkan hasil
$query .= " ORDER BY f.tgl_follow_up DESC, f.created_at DESC";

$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Unit Perumahan - Daftar Follow Up</title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js" defer></script>
<style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2980b9;
            --accent-color: #e74c3c;
            --light-color: #ecf0f1;
            --dark-color: #2c3e50;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            margin-bottom: 20px;
            border: none;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: 600;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .btn-danger {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table thead {
            background-color: var(--dark-color);
            color: white;
        }
        
        .table th {
            font-weight: 600;
        }
        
        .search-container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        
        .badge-call-in { background-color: #3498db; }
        .badge-survey { background-color: #2ecc71; }
        .badge-reserve { background-color: #f39c12; }
        .badge-dp { background-color: #9b59b6; }
        .badge-pemberkasan { background-color: #1abc9c; }
        .badge-wawancara { background-color: #d35400; }
        .badge-analisa { background-color: #34495e; }
        .badge-sp3k { background-color: #27ae60; }
        .badge-reject { background-color: #e74c3c; }
        .badge-akad { background-color: #16a085; }
        .badge-pencairan { background-color: #8e44ad; }
        .badge-cek-fisik { background-color: #f1c40f; color: #000; }
        .badge-bast { background-color: #7f8c8d; }
        .badge-reques { background-color: #c0392b; }
        .badge-topping { background-color: #d35400; }
        .badge-legalitas { background-color: #2c3e50; }
        .badge-komplain { background-color: #e67e22; }
</style>
</head>
<!-- filepath: c:\laragon\www\perum\daftar_followup.php -->
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

  <!-- Followup Section -->
<div class="container mx-auto my-5">
<div class="bg-white p-6 rounded-lg shadow-md mb-4 mt-24">

<!-- message sukses -->
<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
  <div id="success-message" class="bg-green-100 border border-green-400 text-green-700 px-3 py-2 rounded relative mb-3 text-sm flex items-start max-w-sm mx-auto shadow-sm" role="alert">
    <div class="flex-1">
      <strong class="font-bold">Berhasil!</strong>
      <span class="block sm:inline">Data follow-up berhasil ditambahkan.</span>
    </div>
    <button class="absolute top-1 right-1 px-2 py-1" onclick="this.parentElement.style.display='none';">
      <svg class="fill-current h-4 w-4 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
        <path d="M14.348 5.652a1 1 0 00-1.414 0L10 8.586 7.066 5.652a1 1 0 10-1.414 1.414L8.586 10l-2.934 2.934a1 1 0 101.414 1.414L10 11.414l2.934 2.934a1 1 0 001.414-1.414L11.414 10l2.934-2.934a1 1 0 000-1.414z"/>
      </svg>
    </button>
  </div>

  <script>
    setTimeout(function() {
      var message = document.getElementById('success-message');
      if (message) {
        message.style.transition = 'opacity 0.5s ease';
        message.style.opacity = '0';
        setTimeout(function() {
          message.style.display = 'none';
        }, 500);
      }
    }, 5000);
  </script>
<?php endif; ?>
                                          

  <!-- Header Section -->
  <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
    <div>
      <h2 class="text-2xl font-bold text-gray-800 flex items-center">
        <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        Daftar Follow Up
      </h2>
      <p class="text-gray-500 mt-1 text-sm">Riwayat follow up dengan calon konsumen</p>
    </div>
    
    <a href="form_jejak_followup.php" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 font-medium rounded-lg text-sm px-5 py-2.5 flex items-center transition-colors duration-200">
      <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
      </svg>
      Tambah Follow Up
    </a>
  </div>

  <!-- Search and Filter Section -->
  <div class="bg-gray-50 p-4 rounded-lg">
    <form method="GET" action="daftar_followup.php" class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <!-- Search Input -->
      <div>
        <label for="search" class="block mb-2 text-sm font-medium text-gray-700">Cari Nama/Telepon</label>
        <input type="text" id="search" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Cari...">
      </div>
      
      <!-- Status Select -->
      <div>
        <label for="status" class="block mb-2 text-sm font-medium text-gray-700">Status Progres</label>
        <select id="status" name="status" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
          <option value="">Semua Status</option>
          <option value="Call In" <?= (isset($_GET['status']) && $_GET['status'] === 'Call In') ? 'selected' : '' ?>>Call In</option>
          <option value="Survey" <?= (isset($_GET['status']) && $_GET['status'] === 'Survey') ? 'selected' : '' ?>>Survey</option>
          <option value="Reserve" <?= (isset($_GET['status']) && $_GET['status'] === 'Reserve') ? 'selected' : '' ?>>Reserve</option>
          <option value="DP" <?= (isset($_GET['status']) && $_GET['status'] === 'DP') ? 'selected' : '' ?>>DP</option>
          <option value="Pemberkasan" <?= (isset($_GET['status']) && $_GET['status'] === 'Pemberkasan') ? 'selected' : '' ?>>Pemberkasan</option>
          <option value="Wawancara" <?= (isset($_GET['status']) && $_GET['status'] === 'Wawancara') ? 'selected' : '' ?>>Wawancara</option>
          <option value="Analisa" <?= (isset($_GET['status']) && $_GET['status'] === 'Analisa') ? 'selected' : '' ?>>Analisa</option>
          <option value="Sp3k" <?= (isset($_GET['status']) && $_GET['status'] === 'Sp3k') ? 'selected' : '' ?>>Sp3k</option>
          <option value="Reject" <?= (isset($_GET['status']) && $_GET['status'] === 'Reject') ? 'selected' : '' ?>>Reject</option>
          <option value="Akad kredit" <?= (isset($_GET['status']) && $_GET['status'] === 'Akad kredit') ? 'selected' : '' ?>>Akad kredit</option>
          <option value="Pencairan Akad" <?= (isset($_GET['status']) && $_GET['status'] === 'Pencairan Akad') ? 'selected' : '' ?>>Pencairan Akad</option>
          <option value="Cek Fisik Bangunan" <?= (isset($_GET['status']) && $_GET['status'] === 'Cek Fisik Bangunan') ? 'selected' : '' ?>>Cek Fisik Bangunan</option>
          <option value="BAST" <?= (isset($_GET['status']) && $_GET['status'] === 'BAST') ? 'selected' : '' ?>>BAST</option>
          <option value="Reques Bangun" <?= (isset($_GET['status']) && $_GET['status'] === 'Reques Bangun') ? 'selected' : '' ?>>Reques Bangun</option>
          <option value="Pencairan Topping Off" <?= (isset($_GET['status']) && $_GET['status'] === 'Pencairan Topping Off') ? 'selected' : '' ?>>Pencairan Topping Off</option>
          <option value="Pencairan Legalitas" <?= (isset($_GET['status']) && $_GET['status'] === 'Pencairan Legalitas') ? 'selected' : '' ?>>Pencairan Legalitas</option>
          <option value="KOMPLAIN" <?= (isset($_GET['status']) && $_GET['status'] === 'KOMPLAIN') ? 'selected' : '' ?>>KOMPLAIN</option>
        </select>
      </div>
      
      <!-- Date Input -->
      <div>
        <label for="date" class="block mb-2 text-sm font-medium text-gray-700">Tanggal Follow Up</label>
        <input type="date" id="date" name="date" value="<?= htmlspecialchars($_GET['date'] ?? '') ?>" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
      </div>
      
      <!-- Filter and Reset Buttons -->
      <div class="flex items-end gap-2">
        <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 font-medium rounded-lg text-sm px-5 py-2.5 w-full flex items-center justify-center transition-colors duration-200">
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
          </svg>
          Filter
        </button>
        <a href="daftar_followup.php" class="text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 w-full flex items-center justify-center transition-colors duration-200">
          Reset
        </a>
      </div>
    </form>
  </div>
</div>

<!-- Table Data -->
<div class="bg-white rounded-lg shadow-md overflow-hidden">
  <div class="flex justify-between items-center p-4 border-b border-gray-200">
    <h3 class="text-lg font-semibold text-gray-700 flex items-center">
      <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
      </svg>
      Riwayat Follow Up
    </h3>
    <span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
      <?php echo mysqli_num_rows($result); ?> Data
    </span>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500">
      <thead class="text-xs text-gray-700 uppercase bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3">No</th>
          <th scope="col" class="px-6 py-3">Tanggal</th>
          <th scope="col" class="px-6 py-3">Calon Konsumen</th>
          <th scope="col" class="px-6 py-3">Kontak</th>
          <th scope="col" class="px-6 py-3">Status</th>
          <th scope="col" class="px-6 py-3">Keterangan</th>
          <th scope="col" class="px-6 py-3">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $no = 1;
        if(mysqli_num_rows($result) > 0) {
          while($row = mysqli_fetch_assoc($result)) {
            $status_class = strtolower(str_replace(' ', '-', $row['status_progres']));
            $nama_konsumen = !empty($row['nama_konsumen']) ? $row['nama_konsumen'] : $row['calon_konsumen'];
        ?>
        <tr class="bg-white border-b hover:bg-gray-50">
          <td class="px-6 py-4"><?php echo $no++; ?></td>
          <td class="px-6 py-4">
            <?php echo date('d M Y', strtotime($row['tgl_follow_up'])); ?>
            <div class="text-gray-500 text-xs"><?php echo $row['melalui']; ?></div>
          </td>
          <td class="px-6 py-4">
            <div class="font-medium text-gray-900"><?php echo htmlspecialchars($nama_konsumen); ?></div>
            <?php if(!empty($row['pekerjaan'])): ?>
              <div class="text-gray-500 text-xs"><?php echo htmlspecialchars($row['pekerjaan']); ?></div>
            <?php endif; ?>
            <button type="button" class="text-blue-600 text-xs hover:underline mt-1 flex items-center" 
                    data-collapse-toggle="detailKonsumen<?php echo $row['id']; ?>">
              <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
              </svg>
              Detail
            </button>
            <div id="detailKonsumen<?php echo $row['id']; ?>" class="hidden bg-gray-50 p-3 rounded mt-2 space-y-1 text-xs">
              <?php if(!empty($row['alamat_lengkap'])): ?>
                <div><span class="font-semibold">Alamat:</span> <?php echo htmlspecialchars($row['alamat_lengkap']); ?></div>
              <?php endif; ?>
              <?php if(!empty($row['no_hp'])): ?>
                <div><span class="font-semibold">No HP:</span> <?php echo htmlspecialchars($row['no_hp']); ?></div>
              <?php endif; ?>
              <?php if(!empty($row['email'])): ?>
                <div><span class="font-semibold">Email:</span> <?php echo htmlspecialchars($row['email']); ?></div>
              <?php endif; ?>
            </div>
          </td>
          <td class="px-6 py-4">
            <?php if(!empty($row['telp'])): ?>
              <a href="https://wa.me/<?php echo $row['telp']; ?>" class="text-green-600 hover:text-green-800 flex items-center" target="_blank">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-6.29-3.91c.545 1.37 1.676 2.522 3.054 3.069.197.075.396.111.57.111.248 0 .471-.06.644-.122.446-.174.853-.785.992-1.44.174-.694.174-1.285.025-1.459-.149-.173-1.065-.81-1.657-.109-.198.223-.595.669-.793.893-.198.223-.397.26-.644.074-.248-.186-1.065-.794-1.98-1.833-.893-1.012-1.488-2.165-1.676-2.917-.174-.694-.033-1.071.124-1.235.136-.136.298-.186.47-.186.149 0 .298.025.422.075.446.174.744.595.892.893.149.298.248.669.347.967.099.298.198.26.347.074.149-.186.595-.708.744-.893.149-.186.248-.26.422-.26.174 0 .347.074.446.223.099.149.595.744.694 1.016.1.272.174.52.025.818-.149.298-.595.744-1.24 1.337-1.637 1.49-2.336 1.724-3.045 1.686z"/>
                </svg>
                <?php echo $row['telp']; ?>
              </a>
            <?php elseif(!empty($row['no_hp'])): ?>
              <a href="https://wa.me/<?php echo $row['no_hp']; ?>" class="text-green-600 hover:text-green-800 flex items-center" target="_blank">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-6.29-3.91c.545 1.37 1.676 2.522 3.054 3.069.197.075.396.111.57.111.248 0 .471-.06.644-.122.446-.174.853-.785.992-1.44.174-.694.174-1.285.025-1.459-.149-.173-1.065-.81-1.657-.109-.198.223-.595.669-.793.893-.198.223-.397.26-.644.074-.248-.186-1.065-.794-1.98-1.833-.893-1.012-1.488-2.165-1.676-2.917-.174-.694-.033-1.071.124-1.235.136-.136.298-.186.47-.186.149 0 .298.025.422.075.446.174.744.595.892.893.149.298.248.669.347.967.099.298.198.26.347.074.149-.186.595-.708.744-.893.149-.186.248-.26.422-.26.174 0 .347.074.446.223.099.149.595.744.694 1.016.1.272.174.52.025.818-.149.298-.595.744-1.24 1.337-1.637 1.49-2.336 1.724-3.045 1.686z"/>
                </svg>
                <?php echo $row['no_hp']; ?>
              </a>
            <?php else: ?>
              <span class="text-gray-400">-</span>
            <?php endif; ?>
          </td>
          <td class="px-6 py-4">
            <?php 
            // Define status colors based on Flowbite classes
            $status_colors = [
              'prospek' => 'bg-blue-100 text-blue-800',
              'deal' => 'bg-green-100 text-green-800',
              'gagal' => 'bg-red-100 text-red-800',
              'pending' => 'bg-yellow-100 text-yellow-800',
              'follow-up' => 'bg-purple-100 text-purple-800'
            ];
            $status_class = $status_colors[strtolower(str_replace(' ', '-', $row['status_progres']))] ?? 'bg-gray-100 text-gray-800';
            ?>
            <span class="text-xs font-medium px-2.5 py-0.5 rounded-full <?php echo $status_class; ?>">
              <?php echo $row['status_progres']; ?>
            </span>
          </td>
          <td class="px-6 py-4">
            <div class="text-xs">
              <?php echo substr(htmlspecialchars($row['keterangan']), 0, 50); ?>...
            </div>
            <?php if($row['hasil']): ?>
              <div class="mt-1">
                <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-blue-100 text-blue-800">
                  Hasil: <?php echo substr(htmlspecialchars($row['hasil']), 0, 30); ?>...
                </span>
              </div>
            <?php endif; ?>
          </td>
          <td class="px-6 py-4">
            <div class="flex space-x-2">
              <a href="edit_followup.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-900" title="Edit">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                </svg>
              </a>
              <a href="hapus_followup.php?id=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-900" title="Hapus" onclick="return confirm('Yakin hapus data ini?')">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
              </a>
            </div>
          </td>
        </tr>
        <?php 
          }
        } else {
        ?>
        <tr class="bg-white border-b hover:bg-gray-50">
          <td colspan="7" class="px-6 py-4 text-center text-gray-500">
            <svg class="w-5 h-5 inline mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            Tidak ada data follow up
          </td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </div>
  <div class="p-4 border-t border-gray-200 text-xs text-gray-500">
    Terakhir diperbarui: <?php echo date('d M Y H:i:s'); ?>
  </div>
</div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Fungsi untuk filter data
    document.getElementById('search').addEventListener('keyup', function() {
      const searchValue = this.value.toLowerCase();
      const rows = document.querySelectorAll('tbody tr');
      
      rows.forEach(row => {
        const nama = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const telp = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
        
        if(nama.includes(searchValue) || telp.includes(searchValue)) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
    
    // Filter berdasarkan status
    document.getElementById('status').addEventListener('change', function() {
      const statusValue = this.value;
      const rows = document.querySelectorAll('tbody tr');
      
      if(!statusValue) {
        rows.forEach(row => row.style.display = '');
        return;
      }
      
      rows.forEach(row => {
        const status = row.querySelector('td:nth-child(5)').textContent.trim();
        
        if(status === statusValue) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      });
    });
  </script>

</div>

<script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>
<script>
  // Menambahkan delay 5 detik untuk menghilangkan pesan
  setTimeout(function() {
    var message = document.getElementById('success-message');
    if (message) {
      message.style.display = 'none'; // Menyembunyikan pesan setelah 5 detik
    }
  }, 5000);
</script>

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
</body>
</html>
