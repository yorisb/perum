<?php
session_start();
include '../routes/config.php';

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --light-bg: #f8f9fa;
        }
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            border: none;
        }
        .card-header {
            background-color: var(--primary-color);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px 20px;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .table {
            font-size: 14px;
        }
        .table th {
            background-color: #f1f5f9;
            font-weight: 600;
            color: #334155;
        }
        .status-pending {
            background-color: #fef3c7;
            color: #d97706;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 12px;
        }
        .status-approved {
            background-color: #dcfce7;
            color: #16a34a;
            padding: 3px 8px;
            border-radius: 20px;
            font-size: 12px;
        }
        .action-btn {
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            margin-right: 5px;
            transition: all 0.3s;
        }
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .btn-approve {
            background-color: var(--success-color);
            color: white;
        }
        .btn-reject {
            background-color: var(--danger-color);
            color: white;
        }
        .btn-print {
            background-color: var(--primary-color);
            color: white;
        }
        .proof-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 5px;
            cursor: pointer;
            transition: transform 0.3s;
        }
        .proof-image:hover {
            transform: scale(1.5);
            z-index: 100;
            position: relative;
        }
        .amount {
            font-weight: 600;
            color: #1e40af;
        }
        .search-container {
            margin-bottom: 20px;
        }
        .no-data {
            text-align: center;
            padding: 30px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Data Pemasukan</h4>
                <div>
                    <a href="#" class="btn btn-light btn-sm"><i class="fas fa-filter me-1"></i> Filter</a>
                    <a href="#" class="btn btn-light btn-sm"><i class="fas fa-download me-1"></i> Export</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3 search-container">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" placeholder="Cari data pemasukan...">
                            <button class="btn btn-primary">Cari</button>
                        </div>
                    </div>
                    <div class="col-md-6 text-end">
                        <div class="dropdown d-inline-block me-2">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-calendar me-1"></i> Periode
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Hari Ini</a></li>
                                <li><a class="dropdown-item" href="#">Minggu Ini</a></li>
                                <li><a class="dropdown-item" href="#">Bulan Ini</a></li>
                                <li><a class="dropdown-item" href="#">Tahun Ini</a></li>
                                <li><a class="dropdown-item" href="#">Custom</a></li>
                            </ul>
                        </div>
                        <div class="dropdown d-inline-block">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-filter me-1"></i> Status
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Semua</a></li>
                                <li><a class="dropdown-item" href="#">Pending</a></li>
                                <li><a class="dropdown-item" href="#">Approved</a></li>
                                <li><a class="dropdown-item" href="#">Rejected</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Tanggal</th>
                                <th>Asal</th>
                                <th>Unit</th>
                                <th>Keterangan</th>
                                <th>Jumlah</th>
                                <th>No Tanda Terima</th>
                                <th>Bukti</th>
                                <th>Status</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= date('d M Y', strtotime($row['tanggal'])) ?></td>
                                        <td><?= htmlspecialchars($row['asal']) ?></td>
                                        <td><?= $row['unit_id'] ?></td>
                                        <td><?= htmlspecialchars($row['keterangan']) ?></td>
                                        <td class="amount">Rp <?= number_format($row['jumlah'], 2, ',', '.') ?></td>
                                        <td><?= $row['no_tanda_terima'] ?></td>
                                        <td>
                                            <?php if (!empty($row['file_upload']) && file_exists($row['file_upload'])): ?>
                                                <img src="<?= htmlspecialchars($row['file_upload']) ?>" class="proof-image" data-bs-toggle="modal" data-bs-target="#imageModal" data-img="<?= htmlspecialchars($row['file_upload']) ?>">
                                            <?php else: ?>
                                                <span class="text-muted">Tidak ada</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($row['status'] == '0'): ?>
                                                <span class="status-pending"><i class="fas fa-clock me-1"></i> Pending</span>
                                            <?php elseif ($row['status'] == '1'): ?>
                                                <span class="status-approved"><i class="fas fa-check-circle me-1"></i> Approved</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Unknown</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
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
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="10" class="no-data">
                                        <i class="fas fa-database fa-3x mb-3" style="color: #e2e8f0;"></i>
                                        <h5>Tidak ada data pemasukan</h5>
                                        <p class="text-muted">Belum ada data yang tercatat</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-3">
                    <div class="col-md-6">
                        <p class="text-muted">Menampilkan 1 sampai 10 dari 100 entri</p>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="Page navigation" class="float-end">
                            <ul class="pagination">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1">Previous</a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item"><a class="page-link" href="#">2</a></li>
                                <li class="page-item"><a class="page-link" href="#">3</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#">Next</a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bukti Upload</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" class="img-fluid" style="max-height: 70vh;">
                </div>
                <div class="modal-footer">
                    <a href="#" id="downloadImage" class="btn btn-primary"><i class="fas fa-download me-1"></i> Download</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image modal handler
        var imageModal = document.getElementById('imageModal');
        imageModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var imgSrc = button.getAttribute('data-img');
            var modalImg = document.getElementById('modalImage');
            var downloadBtn = document.getElementById('downloadImage');
            
            modalImg.src = imgSrc;
            downloadBtn.href = imgSrc;
            downloadBtn.download = 'bukti_pemasukan_' + new Date().getTime();
        });
    </script>
</body>
</html>