<?php
session_start();

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Proses pembayaran jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bayar_angsuran'])) {
    try {
        include 'routes/config.php';
        
        $angsuran_id = $_POST['angsuran_id'];
        $tgl_bayar = $_POST['tgl_bayar'];
        $metode_bayar = $_POST['metode_bayar'];
        
        // Update status angsuran
        $update_query = $conn->prepare("UPDATE angsuran SET status = 1 WHERE id = ?");
        $update_query->bind_param('i', $angsuran_id);
        
        if ($update_query->execute()) {
            $_SESSION['success_message'] = "Pembayaran angsuran berhasil dicatat!";
            header('Location: pembayaran.php');
            exit();
        } else {
            throw new Exception("Gagal mengupdate status angsuran");
        }
    } catch (Exception $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}

// Konfigurasi pagination
$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

try {
    include 'routes/config.php';

    if ($conn->connect_error) {
        throw new Exception("Koneksi database gagal: " . $conn->connect_error);
    }

    $username = $_SESSION['username'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    if (!$stmt) {
        throw new Exception("Error menyiapkan statement: " . $conn->error);
    }
    $stmt->bind_param('s', $username);
    if (!$stmt->execute()) {
        throw new Exception("Error eksekusi query: " . $stmt->error);
    }
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    if (!$user) {
        throw new Exception("Pengguna tidak ditemukan");
    }

    // Get and clear success message from session
    $loginSuccess = '';
    if (!empty($_SESSION['success_message'])) {
        $loginSuccess = htmlspecialchars($_SESSION['success_message']);
        unset($_SESSION['success_message']);
    }

    // Parameter pencarian
    $search_term = isset($_GET['search']) ? trim($_GET['search']) : '';

    // QUERY UNTUK DATA BELUM LUNAS - DIUBAH ORDER BY a.id ASC
    $unpaid_query = "
        SELECT SQL_CALC_FOUND_ROWS a.id, a.transaksi_id, a.angsuran_amount, a.status, a.tanggal, t.nama_lengkap
        FROM angsuran a
        JOIN transaksi t ON a.transaksi_id = t.id
        WHERE a.status = 0
    ";
    
    // QUERY UNTUK DATA SUDAH LUNAS - DIUBAH ORDER BY a.id ASC
    $paid_query = "
        SELECT SQL_CALC_FOUND_ROWS a.id, a.transaksi_id, a.angsuran_amount, a.status, a.tanggal, t.nama_lengkap
        FROM angsuran a
        JOIN transaksi t ON a.transaksi_id = t.id
        WHERE a.status = 1
    ";

    // Tambahkan filter pencarian jika ada
    if ($search_term !== '') {
        $search_param = "%$search_term%";
        $unpaid_query .= " AND (a.transaksi_id LIKE ? OR t.nama_lengkap LIKE ?)";
        $paid_query .= " AND (a.transaksi_id LIKE ? OR t.nama_lengkap LIKE ?)";
    }

    // Tambahkan sorting dan pagination - DIUBAH MENJADI ORDER BY a.id ASC
    $unpaid_query .= " ORDER BY a.id DESC LIMIT ?, ?";
    $paid_query .= " ORDER BY a.id DESC LIMIT ?, ?";

    // EKSEKUSI QUERY BELUM LUNAS
    $unpaid_stmt = $conn->prepare($unpaid_query);
    if (!$unpaid_stmt) {
        throw new Exception("Error menyiapkan query belum lunas: " . $conn->error);
    }

    $offset = ($current_page - 1) * $items_per_page;

    // Binding parameter untuk unpaid_stmt
    if ($search_term !== '') {
        $unpaid_stmt->bind_param('ssii', $search_param, $search_param, $offset, $items_per_page);
    } else {
        $unpaid_stmt->bind_param('ii', $offset, $items_per_page);
    }

    if (!$unpaid_stmt->execute()) {
        throw new Exception("Error eksekusi query belum lunas: " . $unpaid_stmt->error);
    }
    $unpaid_result = $unpaid_stmt->get_result();

    // Hitung total records untuk pagination belum lunas
    $total_rows_unpaid = $conn->query("SELECT FOUND_ROWS()")->fetch_row()[0];
    $total_pages_unpaid = ceil($total_rows_unpaid / $items_per_page);

    // EKSEKUSI QUERY SUDAH LUNAS
    $paid_stmt = $conn->prepare($paid_query);
    if (!$paid_stmt) {
        throw new Exception("Error menyiapkan query sudah lunas: " . $conn->error);
    }

    // Binding parameter untuk paid_stmt
    if ($search_term !== '') {
        $paid_stmt->bind_param('ssii', $search_param, $search_param, $offset, $items_per_page);
    } else {
        $paid_stmt->bind_param('ii', $offset, $items_per_page);
    }

    if (!$paid_stmt->execute()) {
        throw new Exception("Error eksekusi query sudah lunas: " . $paid_stmt->error);
    }
    $paid_result = $paid_stmt->get_result();

    // Hitung total records untuk pagination sudah lunas
    $total_rows_paid = $conn->query("SELECT FOUND_ROWS()")->fetch_row()[0];
    $total_pages_paid = ceil($total_rows_paid / $items_per_page);

} catch (Exception $e) {
    $error_message = "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Angsuran</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js" defer></script>
</head>

<body class="bg-gray-200 min-h-screen flex">    
    <!-- Sidebar Toggle Button -->
    <button id="sidebarToggle" class="text-gray-500 bg-white p-2 rounded-md border-2 border-gray-500 fixed top-4 left-4 z-50">
        &#9776;
    </button>

    <?php include 'templates/sidebar.php'; ?>

    <div id="main-content" class="flex-1 ml-64 p-6 transition-all duration-300 ease-out">
        <!-- Navbar -->
        <div id="navbar" class="rounded-md fixed top-0 left-0 w-full z-10 transition-all duration-300 ease-out ml-64">
            <?php include 'templates/navbar.php'; ?>
        </div>

        <!-- Header -->
        <div class="rounded-lg max-w-7xl mx-auto p-4 mt-4">
            <header class="bg-white shadow-md mt-12 rounded-lg p-6">
                <h1 class="text-3xl font-extrabold text-gray-800">
                    Data Angsuran
                </h1>
                <p class="text-gray-600 mt-2">
                    Daftar seluruh angsuran berdasarkan transaksi.
                </p>
            </header>

            <!-- Form Pencarian -->
            <form method="get" class="mt-4 flex gap-2 items-center">
                <input type="text" name="search" placeholder="Cari ID Transaksi atau Nama..." 
                    value="<?= htmlspecialchars($search_term) ?>"
                    class="border border-gray-300 rounded px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 w-full max-w-md">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    <i class="fas fa-search mr-1"></i> Cari
                </button>
                <?php if ($search_term): ?>
                    <a href="pembayaran.php" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        <i class="fas fa-times mr-1"></i> Reset
                    </a>
                <?php endif; ?>
            </form>

            <main class="flex-1 p-0 md:p-0 mt-2">
                <div class="bg-white rounded-xl shadow overflow-x-auto mt-4">
                    <?php if (isset($error_message)): ?>
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-md shadow-md mb-4">
                            <?= $error_message ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($loginSuccess)): ?>
                        <div id="login-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md shadow-md flex items-start gap-3 text-sm mb-4 animate-fade-in-down">
                            <i class="fas fa-check-circle mt-0.5 text-green-700 flex-shrink-0"></i>
                            <div class="flex-1">
                                <p class="font-semibold">Berhasil!</p>
                                <p class="text-xs"><?= $loginSuccess ?></p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Tab untuk memisahkan angsuran lunas/belum lunas -->
                    <div class="mb-4 border-b border-gray-200">
                        <ul class="flex flex-wrap -mb-px" id="myTab" data-tabs-toggle="#myTabContent" role="tablist">
                            <li class="mr-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 rounded-t-lg" id="unpaid-tab" data-tabs-target="#unpaid" type="button" role="tab" aria-controls="unpaid" aria-selected="true">
                                    Belum Lunas
                                </button>
                            </li>
                            <li class="mr-2" role="presentation">
                                <button class="inline-block p-4 border-b-2 border-transparent rounded-t-lg hover:text-gray-600 hover:border-gray-300" id="paid-tab" data-tabs-target="#paid" type="button" role="tab" aria-controls="paid" aria-selected="false">
                                    Sudah Lunas
                                </button>
                            </li>
                        </ul>
                    </div>
                    
                    <div id="myTabContent">
                        <!-- Tab Belum Lunas -->
                        <div class="hidden p-4 rounded-lg bg-gray-50" id="unpaid" role="tabpanel" aria-labelledby="unpaid-tab">
                            <?php if (isset($unpaid_result) && $unpaid_result->num_rows > 0): ?>
                                <table class="min-w-full divide-y divide-gray-200" id="unpaidTable">
                                    <thead class="bg-blue-100">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Transaksi</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Angsuran</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Jatuh Tempo</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php 
                                        $no = 1;
                                        while ($installment = $unpaid_result->fetch_assoc()):
                                            // Pastikan tanggal tidak null sebelum diproses
                                            $due_date = !empty($installment['tanggal']) ? strtotime($installment['tanggal']) : 0;
                                            $today = strtotime(date('Y-m-d'));
                                            $sisa_hari = $due_date > 0 ? floor(($due_date - $today) / 86400) : '-';

                                            if ($due_date > 0 && $sisa_hari <= 3) {
                                                $text_color = 'text-red-600';
                                                $badge_color = 'bg-red-100 text-red-800';
                                            } elseif ($due_date > 0 && $sisa_hari <= 7) {
                                                $text_color = 'text-orange-500';
                                                $badge_color = 'bg-orange-100 text-orange-800';
                                            } else {
                                                $text_color = 'text-yellow-600';
                                                $badge_color = 'bg-yellow-100 text-yellow-800';
                                            }
                                        ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?= $no++; ?></td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($installment['transaksi_id']) ?>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($installment['nama_lengkap']) ?>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                                Rp <?= number_format($installment['angsuran_amount'], 0, ',', '.') ?>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-800">
                                                    Belum Lunas
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900 <?= $text_color ?>">
                                                <?= $due_date > 0 ? date('d-m-Y', $due_date) : '-' ?>
                                                <?php if ($due_date > 0 && $sisa_hari >= 0): ?>
                                                <span class="px-2 py-1 rounded-full text-xs <?= $badge_color ?> ml-2">
                                                    <?= $sisa_hari ?> hari lagi
                                                </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                <button onclick="openPaymentModal(
                                                    '<?= $installment['id'] ?>',
                                                    '<?= htmlspecialchars($installment['transaksi_id']) ?>',
                                                    '<?= htmlspecialchars($installment['nama_lengkap']) ?>',
                                                    '<?= number_format($installment['angsuran_amount'], 0, ',', '.') ?>'
                                                )" class="text-purple-600 hover:text-purple-900">
                                                    <i class="fas fa-money-bill-wave mr-1"></i> Bayar
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                
                                <!-- Pagination -->
                                <div class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
                                    <div class="flex-1 flex justify-between sm:hidden">
                                        <?php if ($current_page > 1): ?>
                                            <a href="?page=<?= $current_page - 1 ?><?= $search_term ? '&search='.urlencode($search_term) : '' ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                Sebelumnya
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($current_page < $total_pages_unpaid): ?>
                                            <a href="?page=<?= $current_page + 1 ?><?= $search_term ? '&search='.urlencode($search_term) : '' ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                Selanjutnya
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-sm text-gray-700">
                                                Menampilkan <span class="font-medium"><?= (($current_page - 1) * $items_per_page) + 1 ?></span> sampai <span class="font-medium"><?= min($current_page * $items_per_page, $total_rows_unpaid) ?></span> dari <span class="font-medium"><?= $total_rows_unpaid ?></span> hasil
                                            </p>
                                        </div>
                                        <div>
                                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                                <?php if ($current_page > 1): ?>
                                                    <a href="?page=<?= $current_page - 1 ?><?= $search_term ? '&search='.urlencode($search_term) : '' ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                        <span class="sr-only">Sebelumnya</span>
                                                        <i class="fas fa-chevron-left"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php 
                                                $start_page = max(1, $current_page - 2);
                                                $end_page = min($total_pages_unpaid, $current_page + 2);
                                                
                                                for ($i = $start_page; $i <= $end_page; $i++): 
                                                ?>
                                                    <a href="?page=<?= $i ?><?= $search_term ? '&search='.urlencode($search_term) : '' ?>" class="<?= $i == $current_page ? 'bg-blue-50 border-blue-500 text-blue-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                                        <?= $i ?>
                                                    </a>
                                                <?php endfor; ?>

                                                <?php if ($current_page < $total_pages_unpaid): ?>
                                                    <a href="?page=<?= $current_page + 1 ?><?= $search_term ? '&search='.urlencode($search_term) : '' ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                        <span class="sr-only">Selanjutnya</span>
                                                        <i class="fas fa-chevron-right"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center p-8">
                                    <i class="fas fa-calendar-check text-4xl text-gray-400 mb-4"></i>
                                    <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak ada angsuran belum lunas</h3>
                                    <p class="mt-1 text-sm text-gray-500"><?= $search_term ? 'Hasil pencarian tidak ditemukan' : 'Semua angsuran telah dibayar.' ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Tab Sudah Lunas -->
                        <div class="hidden p-4 rounded-lg bg-gray-50" id="paid" role="tabpanel" aria-labelledby="paid-tab">
                            <?php if (isset($paid_result) && $paid_result->num_rows > 0): ?>
                                <table class="min-w-full divide-y divide-gray-200" id="paidTable">
                                    <thead class="bg-green-100">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Transaksi</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Lengkap</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jumlah Angsuran</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pembayaran</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        <?php 
                                        $no = 1;
                                        while ($installment = $paid_result->fetch_assoc()): 
                                            $due_date = strtotime($installment['tanggal']);
                                        ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500"><?= $no++; ?></td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($installment['transaksi_id']) ?>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                <?= htmlspecialchars($installment['nama_lengkap']) ?>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                                Rp <?= number_format($installment['angsuran_amount'], 0, ',', '.') ?>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm">
                                                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                                    Lunas
                                                </span>
                                            </td>
                                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?= date('d-m-Y', $due_date) ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                                
                                <!-- Pagination untuk yang sudah lunas -->
                                <div class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6">
                                    <div class="flex-1 flex justify-between sm:hidden">
                                        <?php if ($current_page > 1): ?>
                                            <a href="?page=<?= $current_page - 1 ?><?= $search_term ? '&search='.urlencode($search_term) : '' ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                Sebelumnya
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($current_page < $total_pages_paid): ?>
                                            <a href="?page=<?= $current_page + 1 ?><?= $search_term ? '&search='.urlencode($search_term) : '' ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                                Selanjutnya
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-sm text-gray-700">
                                                Menampilkan <span class="font-medium"><?= (($current_page - 1) * $items_per_page) + 1 ?></span> sampai <span class="font-medium"><?= min($current_page * $items_per_page, $total_rows_paid) ?></span> dari <span class="font-medium"><?= $total_rows_paid ?></span> hasil
                                            </p>
                                        </div>
                                        <div>
                                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                                <?php if ($current_page > 1): ?>
                                                    <a href="?page=<?= $current_page - 1 ?><?= $search_term ? '&search='.urlencode($search_term) : '' ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                        <span class="sr-only">Sebelumnya</span>
                                                        <i class="fas fa-chevron-left"></i>
                                                    </a>
                                                <?php endif; ?>

                                                <?php 
                                                $start_page = max(1, $current_page - 2);
                                                $end_page = min($total_pages_paid, $current_page + 2);
                                                
                                                for ($i = $start_page; $i <= $end_page; $i++): 
                                                ?>
                                                    <a href="?page=<?= $i ?><?= $search_term ? '&search='.urlencode($search_term) : '' ?>" class="<?= $i == $current_page ? 'bg-green-50 border-green-500 text-green-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                                        <?= $i ?>
                                                    </a>
                                                <?php endfor; ?>

                                                <?php if ($current_page < $total_pages_paid): ?>
                                                    <a href="?page=<?= $current_page + 1 ?><?= $search_term ? '&search='.urlencode($search_term) : '' ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                                        <span class="sr-only">Selanjutnya</span>
                                                        <i class="fas fa-chevron-right"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </nav>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="text-center p-8">
                                    <i class="fas fa-calendar-check text-4xl text-gray-400 mb-4"></i>
                                    <h3 class="mt-2 text-lg font-medium text-gray-900">Tidak ada angsuran sudah lunas</h3>
                                    <p class="mt-1 text-sm text-gray-500"><?= $search_term ? 'Hasil pencarian tidak ditemukan' : 'Belum ada angsuran yang dibayar.' ?></p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Payment Modal -->
    <div id="paymentModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative mx-auto p-5 border w-full max-w-md shadow-lg rounded-md bg-white">
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
                            <span class="font-semibold">ID Transaksi:</span> 
                            <span id="modalTransaksi" class="font-mono"></span>
                        </p>
                        <p class="text-sm text-gray-700 mb-1">
                            <span class="font-semibold">Nama:</span> 
                            <span id="modalNama" class="font-mono"></span>
                        </p>
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold">Jumlah Angsuran:</span> 
                            Rp <span id="modalAngsuran" class="font-mono"></span>
                        </p>
                    </div>
                    
                    <form id="paymentForm" method="POST" action="pembayaran.php" class="space-y-4">
                        <input type="hidden" name="angsuran_id" id="modalAngsuranId">
                        <input type="hidden" name="bayar_angsuran" value="1">
                        
                        <div>
                            <label for="tgl_bayar" class="block text-sm font-medium text-gray-700 text-left mb-1">Tanggal Pembayaran</label>
                            <input type="date" name="tgl_bayar" id="tgl_bayar" 
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm" 
                                required value="<?= date('Y-m-d') ?>">
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

    <script>
        // Sidebar Toggle Script
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

                if (navbar) {
                    navbar.classList.toggle('ml-64', !isHidden);
                    navbar.classList.toggle('pl-16', isHidden);
                }
            });

            // Close sidebar when clicking outside on mobile
            document.addEventListener('click', function(event) {
                if (window.innerWidth < 1024 && 
                    !sidebar.contains(event.target) && 
                    !sidebarToggle.contains(event.target) &&
                    !sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.add('-translate-x-full');
                    mainContent.classList.add('ml-0');
                    if (navbar) {
                        navbar.classList.add('pl-16');
                        navbar.classList.remove('ml-64');
                    }
                }
            });

            // Auto-hide success message after 5 seconds
            setTimeout(() => {
                const alert = document.getElementById('login-alert');
                if (alert) {
                    alert.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => alert.remove(), 500);
                }
            }, 5000);

            // Initialize tabs
            const tabs = document.querySelectorAll('[data-tabs-toggle] [role="tab"]');
            tabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    const target = document.querySelector(this.getAttribute('data-tabs-target'));
                    
                    // Hide all tab contents
                    document.querySelectorAll('[role="tabpanel"]').forEach(panel => {
                        panel.classList.add('hidden');
                    });
                    
                    // Show selected tab content
                    target.classList.remove('hidden');
                    
                    // Update active tab styling
                    tabs.forEach(t => {
                        t.classList.remove('border-blue-500', 'text-blue-600');
                        t.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    });
                    
                    this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
                    this.classList.add('border-blue-500', 'text-blue-600');
                });
            });
            
            // Activate first tab by default
            if (tabs.length > 0) {
                tabs[0].click();
            }
        });

        // Payment modal functions
        function openPaymentModal(angsuranId, transaksiId, namaLengkap, angsuranAmount) {
            document.getElementById('modalAngsuranId').value = angsuranId;
            document.getElementById('modalTransaksi').textContent = transaksiId;
            document.getElementById('modalNama').textContent = namaLengkap;
            document.getElementById('modalAngsuran').textContent = angsuranAmount;
            document.getElementById('paymentModal').classList.remove('hidden');
            
            document.getElementById('tgl_bayar').focus();
        }

        function closePaymentModal() {
            document.getElementById('paymentModal').classList.add('hidden');
        }
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

        /* Tab styling */
        [role="tab"] {
            border-bottom-width: 2px;
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 500;
            color: #6b7280;
            border-color: transparent;
        }

        [role="tab"]:hover {
            color: #374151;
            border-color: #d1d5db;
        }

        [role="tab"].border-blue-500 {
            color: #3b82f6;
            border-color: #3b82f6;
        }
    </style>
</body>
</html>