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

$role = $_SESSION['role']; // owner, admin, user, marketing

// Process Approve
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    $conn->query("UPDATE pemasukan SET status = '1' WHERE id = $id");
    header("Location: tampil_pemasukan.php");
    exit;
}

// Process Reject
if (isset($_GET['reject'])) {
    $id = (int)$_GET['reject'];
    $conn->query("DELETE FROM pemasukan WHERE id = $id");
    header("Location: tampil_pemasukan.php");
    exit;
}

$query = "SELECT * FROM pemasukan ORDER BY tanggal DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pemasukan</title>
    <link rel="icon" href="image/logo.ico" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
    <style>
        .proof-image {
            width: 2.5rem;
            height: 2.5rem;
            object-fit: cover;
            border-radius: 0.25rem;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .proof-image:hover {
            transform: scale(1.1);
        }
        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        .badge-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border-radius: 0.25rem;
            transition: all 0.2s;
        }
        .btn-approve {
            color: #10b981;
            background-color: #ecfdf5;
        }
        .btn-approve:hover {
            background-color: #d1fae5;
        }
        .btn-reject {
            color: #ef4444;
            background-color: #fee2e2;
        }
        .btn-reject:hover {
            background-color: #fecaca;
        }
        .btn-print {
            color: #3b82f6;
            background-color: #eff6ff;
        }
        .btn-print:hover {
            background-color: #dbeafe;
        }
        #sidebar {
            transition: transform 0.3s ease-out;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .-translate-x-full {
            transform: translateX(-100%);
        }
        #main-content, #navbar {
            transition: margin-left 0.3s ease-out, padding-left 0.3s ease-out;
        }
    </style>
</head>
<body class="bg-gray-200 min-h-screen">
  <!-- Sidebar Toggle Button -->
  <button id="sidebarToggle" class="text-gray-500 bg-white p-2 rounded-md border-2 border-gray-500 fixed top-4 left-4 z-50">
    &#9776; <!-- Hamburger Icon -->
  </button>

    <!-- Sidebar -->
    <?php include 'templates/sidebar.php'; ?>

    <!-- Main Content -->
    <div id="main-content" class="flex-1 ml-64 p-6 transition-all duration-300">
        <!-- Navbar -->
        <div id="navbar" class=" rounded-md fixed top-0 left-0 w-full z-10 transition-all duration-300 ease-out ml-64">
        <?php include 'templates/navbar.php'; ?>
        </div>

        <!-- Content -->
        <div class="pt-20 pb-6 px-4 sm:px-6 lg:px-8">
            <?php if (!empty($loginSuccess)): ?>
                <div id="login-alert" class="fixed top-6 left-1/2 transform -translate-x-1/2 w-80 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md shadow-md flex items-start gap-3 text-sm animate-fade-in-down z-50 transition-opacity duration-500">
                    <i class="fas fa-check-circle mt-0.5 text-green-700 flex-shrink-0"></i>
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

            <div class="max-w-7xl mx-auto">
                <div class="bg-white rounded-lg shadow">
                    <div class="border-b border-gray-200 px-6 py-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-money-bill-wave mr-2 text-indigo-600"></i>
                                Data Pemasukan
                            </h2>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button class="px-3 py-1.5 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-filter mr-1"></i> Filter
                            </button>
                            <button class="px-3 py-1.5 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                <i class="fas fa-download mr-1"></i> Export
                            </button>
                            <a href="form_pemasukan.php" class="px-3 py-1.5 text-sm bg-indigo-600 rounded-md text-white hover:bg-indigo-700">
                                <i class="fas fa-plus mr-1"></i> Tambah
                            </a>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                    <input type="text" class="pl-10 pr-4 py-2 border border-gray-300 rounded-md w-full focus:ring-indigo-500 focus:border-indigo-500" placeholder="Cari data pemasukan...">
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 justify-end">
                                <div class="relative">
                                    <button class="px-3 py-1.5 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 flex items-center">
                                        <i class="fas fa-calendar mr-1"></i> Periode
                                    </button>
                                </div>
                                <div class="relative">
                                    <button class="px-3 py-1.5 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 flex items-center">
                                        <i class="fas fa-filter mr-1"></i> Status
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asal</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Tanda Terima</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bukti</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?= $no++ ?></td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($row['asal']) ?></td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?= $row['unit_id'] ?></td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($row['keterangan']) ?></td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">Rp <?= number_format($row['jumlah'], 2, ',', '.') ?></td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500"><?= $row['no_tanda_terima'] ?></td>
                                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">
                                                    <?php if (!empty($row['file_upload']) && file_exists($row['file_upload'])): ?>
                                                        <img src="<?= htmlspecialchars($row['file_upload']) ?>" class="proof-image" data-bs-toggle="modal" data-bs-target="#imageModal" data-img="<?= htmlspecialchars($row['file_upload']) ?>">
                                                    <?php else: ?>
                                                        <span class="text-gray-400">-</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <?php if ($row['status'] == '0'): ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                            <i class="fas fa-clock mr-1"></i> Pending
                                                        </span>
                                                    <?php elseif ($row['status'] == '1'): ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-check-circle mr-1"></i> Approved
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">Unknown</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <div class="flex items-center gap-1">
                                                        <?php if ($role == 'owner' || $role == 'admin'): ?>
                                                            <?php if ($row['status'] == '0'): ?>
                                                                <a href="?approve=<?= $row['id'] ?>" class="action-btn btn-approve" title="Approve">
                                                                    <i class="fas fa-check"></i>
                                                                </a>
                                                                <a href="?reject=<?= $row['id'] ?>" class="action-btn btn-reject" title="Reject" onclick="return confirm('Yakin ingin menolak data ini?')">
                                                                    <i class="fas fa-times"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                            <?php if ($row['status'] == '1'): ?>
                                                                <a href="cetak_pemasukan.php?id=<?= $row['id'] ?>" target="_blank" class="action-btn btn-print" title="Print">
                                                                    <i class="fas fa-print"></i>
                                                                </a>
                                                            <?php endif; ?>
                                                        <?php elseif (($role == 'user' || $role == 'marketing') && $row['status'] == '1'): ?>
                                                            <a href="cetak_pemasukan.php?id=<?= $row['id'] ?>" target="_blank" class="action-btn btn-print" title="Print">
                                                                <i class="fas fa-print"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <span class="text-gray-400">-</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="10" class="px-6 py-8 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <i class="fas fa-database text-4xl mb-3 text-gray-300"></i>
                                                    <h5 class="text-lg font-medium text-gray-700">Tidak ada data pemasukan</h5>
                                                    <p class="text-gray-500">Belum ada data yang tercatat</p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-6 flex flex-col sm:flex-row justify-between items-center gap-4">
                            <div>
                                <p class="text-sm text-gray-600">
                                    Menampilkan 1 sampai 10 dari 100 entri
                                </p>
                            </div>
                            <nav class="flex items-center space-x-2">
                                <button class="px-3 py-1 rounded border bg-white text-gray-600 disabled">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="px-3 py-1 rounded border bg-indigo-600 text-white">1</button>
                                <button class="px-3 py-1 rounded border bg-white text-gray-600 hover:bg-gray-50">2</button>
                                <button class="px-3 py-1 rounded border bg-white text-gray-600 hover:bg-gray-50">3</button>
                                <button class="px-3 py-1 rounded border bg-white text-gray-600 hover:bg-gray-50">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade hidden fixed inset-0 z-50 overflow-y-auto" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" aria-hidden="true"></div>
            <div class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all max-w-4xl w-full">
                <div class="flex justify-between items-center px-6 py-4 border-b">
                    <h3 class="text-lg font-semibold">Bukti Upload</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="p-6">
                    <img id="modalImage" src="" class="mx-auto rounded max-h-[70vh]">
                </div>
                <div class="px-6 py-4 border-t flex justify-end gap-3">
                    <a href="#" id="downloadImage" class="px-3 py-1.5 text-sm bg-indigo-600 rounded-md text-white hover:bg-indigo-700">
                        <i class="fas fa-download mr-1"></i> Download
                    </a>
                    <button type="button" class="px-3 py-1.5 text-sm border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50" onclick="closeModal()">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>

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