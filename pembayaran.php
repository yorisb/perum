<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Include config dengan error handling
try {
    include 'routes/config.php';
    
    // Validasi koneksi database
    if ($conn->connect_error) {
        throw new Exception("Koneksi database gagal: " . $conn->connect_error);
    }

    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    if (!$stmt) {
        throw new Exception("Error preparing statement: " . $conn->error);
    }
    
    $stmt->bind_param('s', $username);
    if (!$stmt->execute()) {
        throw new Exception("Error executing query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    if (!$user) {
        throw new Exception("User tidak ditemukan");
    }

    // Ambil dan hapus pesan sukses dari session
    $loginSuccess = '';
    if (!empty($_SESSION['success_message'])) {
        $loginSuccess = htmlspecialchars($_SESSION['success_message']);
        unset($_SESSION['success_message']);
    }

    // Ambil transaksi dengan rencana_tgl_bayar_pembayaran dalam 14 hari ke depan
    $today = date('Y-m-d');
    $fourteen_days = date('Y-m-d', strtotime('+14 days'));

    $query = $conn->prepare("
        SELECT no_transaksi, nama_unit, id_konsumen, rencana_tgl_bayar_pembayaran, total_akhir
        FROM transaksi
        WHERE rencana_tgl_bayar_pembayaran BETWEEN ? AND ?
        ORDER BY rencana_tgl_bayar_pembayaran ASC
    ");
    
    if (!$query) {
        throw new Exception("Error preparing transaction query: " . $conn->error);
    }
    
    $query->bind_param('ss', $today, $fourteen_days);
    if (!$query->execute()) {
        throw new Exception("Error executing transaction query: " . $query->error);
    }
    
    $transactions_result = $query->get_result();

} catch (Exception $e) {
    $error_message = "Terjadi kesalahan: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tenggat Waktu Pembayaran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js" defer></script>
</head>
<body class="bg-gray-100 min-h-screen flex">    

  <!-- Sidebar Toggle Button -->
  <button id="sidebarToggle" class="text-gray-500 bg-white p-2 rounded-md border-2 border-gray-500 fixed top-4 left-4 z-50">
    &#9776; <!-- Hamburger Icon -->
  </button>

    <?php include 'templates/sidebar.php'; ?>

  <div id="main-content" class="flex-1 ml-64 p-6 transition-all duration-300 ease-out">
    <!-- Navbar -->
    <div id="navbar" class=" rounded-md fixed top-0 left-0 w-full z-10 transition-all duration-300 ease-out ml-64">
      <?php include 'templates/navbar.php'; ?>
    </div>

        <main class="flex-1 p-4 md:p-6 bg-gray-100 mt-8">
            <div class="max-w-6xl mx-auto">
                <div class="mb-6">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-800 mb-2">Notifikasi Tenggat Waktu Pembayaran (H-14)</h1>
                    <p class="text-gray-500">Berikut adalah transaksi yang akan jatuh tempo pembayaran dalam 14 hari ke depan.</p>
                </div>
                
                <?php if (isset($error_message)): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md shadow-md mb-4">
                        <?= $error_message ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($loginSuccess)): ?>
                    <div id="login-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md shadow-md flex items-start gap-3 text-sm mb-4 animate-fade-in-down">
                        <i class="fas fa-check-circle mt-0.5 text-green-700 flex-shrink-0"></i>
                        <div class="flex-1">
                            <p class="font-semibold">Login berhasil!</p>
                            <p class="text-xs"><?= $loginSuccess ?></p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <?php if (isset($transactions_result) && $transactions_result->num_rows > 0): ?>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="notifikasiTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Transaksi</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Konsumen</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tgl Jatuh Tempo</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sisa Hari</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php 
                                    $no = 1; 
                                    while ($row = $transactions_result->fetch_assoc()): 
                                        $due_date = strtotime($row['rencana_tgl_bayar_pembayaran']);
                                        $sisa_hari = ($due_date - strtotime($today)) / 86400;
                                        $sisa_hari = floor($sisa_hari);
                                        
                                        // Tentukan warna teks berdasarkan sisa hari
                                        if ($sisa_hari <= 3) {
                                            $text_color = 'text-red-600';
                                            $badge_color = 'bg-red-100 text-red-800';
                                        } elseif ($sisa_hari <= 7) {
                                            $text_color = 'text-orange-500';
                                            $badge_color = 'bg-orange-100 text-orange-800';
                                        } else {
                                            $text_color = 'text-yellow-600';
                                            $badge_color = 'bg-yellow-100 text-yellow-800';
                                        }
                                        
                                        // Get payment history for this transaction
                                        $history_query = $conn->prepare("
                                            SELECT * FROM transaksi 
                                            WHERE no_transaksi = ?
                                            ORDER BY tgl_transaksi DESC
                                        ");
                                        $history_query->bind_param('s', $row['no_transaksi']);
                                        $history_query->execute();
                                        $history_result = $history_query->get_result();
                                        $history_count = $history_result->num_rows;
                                    ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?= $no++; ?></td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?= htmlspecialchars($row['no_transaksi']); ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= htmlspecialchars($row['nama_unit']); ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= htmlspecialchars($row['id_konsumen']); ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?= date('d-m-Y', $due_date); ?>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-bold <?= $text_color ?>">
                                            <span class="px-2 py-1 rounded-full text-xs <?= $badge_color ?>">
                                                <?= $sisa_hari ?> hari lagi
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <!-- Detail Button -->
                                            <button type="button" 
                                                onclick="togglePaymentHistory('<?= htmlspecialchars($row['no_transaksi']) ?>')"
                                                class="text-blue-600 hover:text-blue-900 focus:outline-none">
                                                <i class="fas fa-history mr-1"></i> Riwayat (<?= $history_count ?>)
                                            </button>
                                            
                                            <!-- Edit Button -->
                                            <a href="edit_transaksi.php?id=<?= htmlspecialchars($row['no_transaksi']) ?>" 
                                               class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-edit mr-1"></i> Edit
                                            </a>
                                            
                                            <!-- Payment Button -->
                                            <button onclick="openPaymentModal(
                                                '<?= htmlspecialchars($row['no_transaksi']) ?>', 
                                                '<?= number_format($row['total_akhir'], 0, ',', '.') ?>'
                                            )" class="text-purple-600 hover:text-purple-900">
                                                <i class="fas fa-money-bill-wave mr-1"></i> Bayar
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Payment History Row -->
                                    <tr id="history-<?= htmlspecialchars($row['no_transaksi']) ?>" class="hidden bg-gray-50">
                                        <td colspan="7" class="px-4 py-4">
                                            <div class="overflow-x-auto">
                                                <?php if (isset($history_result) && $history_result->num_rows > 0): ?>
                                                    <table class="min-w-full divide-y divide-gray-200 text-xs">
                                                        <thead class="bg-gray-100">
                                                            <tr>
                                                                <th class="px-3 py-2 text-left">Tanggal</th>
                                                                <th class="px-3 py-2 text-left">Jumlah</th>
                                                                <th class="px-3 py-2 text-left">Metode</th>
                                                                <th class="px-3 py-2 text-left">Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody class="bg-white divide-y divide-gray-200">
                                                            <?php while ($history = $history_result->fetch_assoc()): ?>
                                                                <tr>
                                                                    <td class="px-3 py-2 whitespace-nowrap">
                                                                        <?= date('d/m/Y', strtotime($history['tgl_transaksi'])) ?>
                                                                    </td>
                                                                    <td class="px-3 py-2 whitespace-nowrap">
                                                                        Rp <?= number_format($history['total_akhir'], 0, ',', '.') ?>
                                                                    </td>
                                                                    <td class="px-3 py-2 whitespace-nowrap">
                                                                        <?= htmlspecialchars($history['cara_pembayaran']) ?>
                                                                    </td>
                                                                    <td class="px-3 py-2 whitespace-nowrap">
                                                                        <?php 
                                                                            $status_class = $history['status_tanda_jadi'] === 'Masuk' 
                                                                                ? 'bg-green-100 text-green-800' 
                                                                                : 'bg-yellow-100 text-yellow-800';
                                                                        ?>
                                                                        <span class="px-2 py-1 rounded-full <?= $status_class ?>">
                                                                            <?= htmlspecialchars($history['status_tanda_jadi']) ?>
                                                                        </span>
                                                                    </td>
                                                                </tr>
                                                            <?php endwhile; ?>
                                                        </tbody>
                                                    </table>
                                                <?php else: ?>
                                                    <div class="text-center py-4 text-gray-500">
                                                        Belum ada riwayat pembayaran
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>

                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center p-8">
                            <i class="fas fa-calendar-check text-4xl text-gray-400 mb-4"></i>
                            <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak ada notifikasi</h3>
                            <p class="mt-1 text-sm text-gray-500">Tidak ada transaksi yang akan jatuh tempo dalam 14 hari ke depan.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Modal Pembayaran -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full md:w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Konfirmasi Pembayaran</h3>
                    <button onclick="closePaymentModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="mt-2 px-4 py-3">
                    <div class="bg-blue-50 p-3 rounded-md mb-4">
                        <p class="text-sm text-gray-700 mb-1">
                            <span class="font-semibold">No. Transaksi:</span> 
                            <span id="modalTransaksi" class="font-mono"></span>
                        </p>
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold">Total Pembayaran:</span> 
                            Rp <span id="modalTotal" class="font-mono"></span>
                        </p>
                    </div>
                    
                    <form id="paymentForm" method="POST" action="proses_pembayaran.php" class="space-y-4">
                        <input type="hidden" name="no_transaksi" id="no_transaksi">
                        
                        <div>
                            <label for="jumlah_bayar" class="block text-sm font-medium text-gray-700 text-left mb-1">Jumlah Bayar</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">Rp</span>
                                <input type="text" name="jumlah_bayar" id="jumlah_bayar" 
                                    class="pl-10 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                    required>
                            </div>
                        </div>
                        
                        <div>
                            <label for="metode_bayar" class="block text-sm font-medium text-gray-700 text-left mb-1">Metode Pembayaran</label>
                            <select name="metode_bayar" id="metode_bayar" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                required>
                                <option value="">Pilih Metode</option>
                                <option value="Transfer Bank">Transfer Bank</option>
                                <option value="Tunai">Tunai</option>
                                <option value="Kartu Kredit">Kartu Kredit</option>
                                <option value="Virtual Account">Virtual Account</option>
                            </select>
                        </div>
                        
                        <div>
                            <label for="tgl_transaksi" class="block text-sm font-medium text-gray-700 text-left mb-1">Tanggal Pembayaran</label>
                            <input type="date" name="tgl_transaksi" id="tgl_transaksi" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                required value="<?= date('Y-m-d') ?>">
                        </div>
                        
                        
                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" onclick="closePaymentModal()" 
                                class="px-4 py-2 bg-gray-200 text-gray-800 text-sm font-medium rounded-md shadow-sm hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500">
                                Batal
                            </button>
                            <button type="submit" 
                                class="px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <i class="fas fa-check-circle mr-1"></i> Konfirmasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.tailwindcss.min.js"></script>

  
    <script>
        // Fungsi untuk toggle history pembayaran
        function togglePaymentHistory(noTransaksi) {
            const historyRow = document.getElementById(`history-${noTransaksi}`);
            historyRow.classList.toggle('hidden');
            
            // Scroll ke row yang dibuka
            historyRow.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        
        // Fungsi untuk modal pembayaran
        function openPaymentModal(noTransaksi, total) {
            document.getElementById('modalTransaksi').textContent = noTransaksi;
            document.getElementById('modalTotal').textContent = total;
            document.getElementById('no_transaksi').value = noTransaksi;
            document.getElementById('jumlah_bayar').value = total.replace(/\./g, '');
            document.getElementById('paymentModal').classList.remove('hidden');
            
            // Set focus ke input jumlah bayar
            document.getElementById('jumlah_bayar').focus();
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }

        // Format input jumlah bayar
        document.getElementById('jumlah_bayar').addEventListener('input', function(e) {
            let value = e.target.value.replace(/[^\d]/g, '');
            e.target.value = new Intl.NumberFormat('id-ID').format(value);
        });

        // Submit form pembayaran
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Format jumlah bayar sebelum submit
            let jumlahBayar = document.getElementById('jumlah_bayar');
            jumlahBayar.value = jumlahBayar.value.replace(/\./g, '');
            
            // Kirim form via AJAX atau langsung submit
            this.submit();
        });

        $(document).ready(function() {
            $('#notifikasiTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Cari...",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                order: [[4, 'asc']], // Default sorting by due date
                columnDefs: [
                    { responsivePriority: 1, targets: 0 }, // No
                    { responsivePriority: 2, targets: 1 }, // No Transaksi
                    { responsivePriority: 4, targets: 4 }, // Tgl Jatuh Tempo
                    { responsivePriority: 3, targets: 5 }, // Sisa Hari
                    { responsivePriority: 5, targets: -1 } // Aksi
                ]
            });

            // Auto-hide success message after 5 seconds
            setTimeout(() => {
                const alert = document.getElementById('login-alert');
                if (alert) {
                    alert.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => alert.remove(), 500);
                }
            }, 5000);
        });

        // Sidebar Toggle for mobile
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const mainContent = document.getElementById('main-content');
            const navbar = document.getElementById('navbar');

            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('-translate-x-full');
                const isHidden = sidebar.classList.contains('-translate-x-full');

                // Untuk desktop
                if (window.innerWidth >= 1024) {
                    mainContent.classList.toggle('lg:ml-64', !isHidden);
                    mainContent.classList.toggle('ml-0', isHidden);
                    if (navbar) {
                        navbar.classList.toggle('ml-64', !isHidden);
                        navbar.classList.toggle('pl-16', isHidden);
                    }
                } else {
                    // Untuk mobile
                    mainContent.classList.toggle('ml-0', isHidden);
                }
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (
                    window.innerWidth < 1024 &&
                    !sidebar.contains(event.target) &&
                    !sidebarToggle.contains(event.target) &&
                    !sidebar.classList.contains('-translate-x-full')
                ) {
                    sidebar.classList.add('-translate-x-full');
                    mainContent.classList.add('ml-0');
                }
            });
        });
    </script>

    <style>
        #sidebar {
            transition: transform 0.3s ease-out;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            z-index: 40;
        }

        .-translate-x-full {
            transform: translateX(-100%);
        }

        /* Animations */
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in-down {
            animation: fadeInDown 0.5s ease-out;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            #main-content {
                margin-left: 0 !important;
            }
            
            #sidebar {
                position: fixed;
                width: 75%;
            }
        }
    </style>
</body>
</html>