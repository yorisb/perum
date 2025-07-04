<?php
session_start();
require_once 'koneksi.php';

// Query dengan JOIN ke tabel calon_konsumen
$query = "SELECT f.*, 
                 c.nama_lengkap AS nama_konsumen,
                 c.no_hp,
                 c.email,
                 c.alamat_lengkap,
                 c.pekerjaan
          FROM jejak_follow_up f
          LEFT JOIN calon_konsumen c ON f.calon_konsumen = c.id
          ORDER BY f.tgl_follow_up DESC, f.created_at DESC";
$result = mysqli_query($koneksi, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Follow Up Konsumen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-people-fill me-2"></i>Follow Up Konsumen
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="daftar_followup.php">
                            <i class="bi bi-list-ul me-1"></i>Daftar Follow Up
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="form_jejak_followup.php">
                            <i class="bi bi-plus-circle me-1"></i>Tambah Baru
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        <!-- Header dan Filter -->
        <div class="row mb-4">
            <div class="col-md-6">
                <h2 class="fw-bold text-dark">
                    <i class="bi bi-clock-history me-2"></i>Daftar Follow Up
                </h2>
                <p class="text-muted">Riwayat follow up dengan calon konsumen</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="tambah_followup.php" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Follow Up
                </a>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="search-container mb-4">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <label for="search" class="form-label">Cari Nama/Telepon</label>
                    <input type="text" class="form-control" id="search" placeholder="Cari...">
                </div>
                <div class="col-md-3 mb-2">
                    <label for="status" class="form-label">Status Progres</label>
                    <select class="form-select" id="status">
                        <option value="">Semua Status</option>
                        <option value="Call In">Call In</option>
                        <option value="Survey">Survey</option>
                        <option value="Reserve">Reserve</option>
                        <option value="DP">DP</option>
                        <option value="Pemberkasan">Pemberkasan</option>
                        <option value="Wawancara">Wawancara</option>
                        <option value="Analisa">Analisa</option>
                        <option value="Sp3k">Sp3k</option>
                        <option value="Reject">Reject</option>
                        <option value="Akad kredit">Akad kredit</option>
                        <option value="Pencairan Akad">Pencairan Akad</option>
                        <option value="Cek Fisik Bangunan">Cek Fisik Bangunan</option>
                        <option value="BAST">BAST</option>
                        <option value="Reques Bangun">Reques Bangun</option>
                        <option value="Pencairan Topping Off">Pencairan Topping Off</option>
                        <option value="Pencairan Legalitas">Pencairan Legalitas</option>
                        <option value="KOMPLAIN">KOMPLAIN</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
                    <label for="date" class="form-label">Tanggal Follow Up</label>
                    <input type="date" class="form-control" id="date">
                </div>
                <div class="col-md-2 d-flex align-items-end mb-2">
                    <button class="btn btn-primary w-100">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="bi bi-table me-2"></i>Riwayat Follow Up</span>
                <span class="badge bg-light text-dark"><?php echo mysqli_num_rows($result); ?> Data</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
    <thead>
        <tr>
            <th width="5%">No</th>
            <th width="15%">Tanggal</th>
            <th width="20%">Calon Konsumen</th>
            <th width="15%">Kontak</th>
            <th width="15%">Status</th>
            <th width="20%">Keterangan</th>
            <th width="10%">Aksi</th>
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
        <tr>
            <td><?php echo $no++; ?></td>
            <td>
                <?php echo date('d M Y', strtotime($row['tgl_follow_up'])); ?>
                <small class="d-block text-muted"><?php echo $row['melalui']; ?></small>
            </td>
            <td>
                <strong><?php echo htmlspecialchars($nama_konsumen); ?></strong>
                <?php if(!empty($row['pekerjaan'])): ?>
                    <small class="d-block text-muted"><?php echo htmlspecialchars($row['pekerjaan']); ?></small>
                <?php endif; ?>
                <button class="btn btn-sm btn-link p-0 mt-1" type="button" data-bs-toggle="collapse" 
                        data-bs-target="#detailKonsumen<?php echo $row['id']; ?>" 
                        aria-expanded="false" aria-controls="detailKonsumen<?php echo $row['id']; ?>">
                    <small><i class="bi bi-chevron-down"></i> Detail</small>
                </button>
                <div class="collapse" id="detailKonsumen<?php echo $row['id']; ?>">
                    <div class="card card-body small p-2 mt-2">
                        <?php if(!empty($row['alamat_lengkap'])): ?>
                            <div><strong>Alamat:</strong> <?php echo htmlspecialchars($row['alamat_lengkap']); ?></div>
                        <?php endif; ?>
                        <?php if(!empty($row['no_hp'])): ?>
                            <div><strong>No HP:</strong> <?php echo htmlspecialchars($row['no_hp']); ?></div>
                        <?php endif; ?>
                        <?php if(!empty($row['email'])): ?>
                            <div><strong>Email:</strong> <?php echo htmlspecialchars($row['email']); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </td>
            <td>
                <?php if(!empty($row['telp'])): ?>
                    <a href="https://wa.me/<?php echo $row['telp']; ?>" class="text-decoration-none" target="_blank">
                        <i class="bi bi-whatsapp text-success me-1"></i><?php echo $row['telp']; ?>
                    </a>
                <?php elseif(!empty($row['no_hp'])): ?>
                    <a href="https://wa.me/<?php echo $row['no_hp']; ?>" class="text-decoration-none" target="_blank">
                        <i class="bi bi-whatsapp text-success me-1"></i><?php echo $row['no_hp']; ?>
                    </a>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
            </td>
            <td>
                <span class="status-badge badge-<?php echo $status_class; ?>">
                    <?php echo $row['status_progres']; ?>
                </span>
            </td>
            <td>
                <small><?php echo substr(htmlspecialchars($row['keterangan']), 0, 50); ?>...</small>
                <?php if($row['hasil']): ?>
                    <div class="mt-1">
                        <span class="badge bg-info">Hasil: <?php echo substr(htmlspecialchars($row['hasil']), 0, 30); ?>...</span>
                    </div>
                <?php endif; ?>
            </td>
            <td>
                <div class="d-flex">
                    <a href="edit_followup.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <a href="hapus_followup.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Yakin hapus data ini?')">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </td>
        </tr>
        <?php 
            }
        } else {
        ?>
        <tr>
            <td colspan="7" class="text-center py-4 text-muted">
                <i class="bi bi-exclamation-circle me-1"></i>Tidak ada data follow up
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>
                </div>
            </div>
            <div class="card-footer text-muted">
                <small>Terakhir diperbarui: <?php echo date('d M Y H:i:s'); ?></small>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="bi bi-building me-2"></i>Nama Perusahaan</h5>
                    <p class="text-muted">Sistem manajemen follow up calon konsumen</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="text-muted mb-0">&copy; <?php echo date('Y'); ?> All Rights Reserved</p>
                </div>
            </div>
        </div>
    </footer>

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
</body>
</html>