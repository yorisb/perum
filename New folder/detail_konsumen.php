<?php
$conn = new mysqli("srv143.niagahoster.com", "n1572337_developer", "bad_cookies8080", "n1572337_developer");

$kode = $_GET['kode'];
$result = $conn->query("SELECT * FROM calon_konsumen WHERE kode = '$kode'");
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Calon Konsumen - <?= $data['nama_lengkap'] ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #f8f9fa;
            --accent-color: #e74c3c;
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
            font-weight: 600;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
            width: 200px;
        }
        
        .info-value {
            color: #333;
        }
        
        .section-divider {
            border-left: 4px solid var(--primary-color);
            padding-left: 15px;
            margin: 20px 0;
        }
        
        .back-btn {
            background-color: var(--primary-color);
            color: white;
            border: none;
            transition: all 0.3s;
        }
        
        .back-btn:hover {
            background-color: #2980b9;
            transform: translateX(-3px);
        }
        
        .file-link {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .file-link:hover {
            text-decoration: underline;
        }
        
        .badge-status {
            background-color: #2ecc71;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">
                <i class="bi bi-person-badge me-2"></i>Detail Calon Konsumen
            </h1>
            <a href="daftar_konsumen.php" class="btn back-btn">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
        
        <div class="row">
            <div class="col-md-4">
                <!-- Profile Summary Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-person-circle me-2"></i>Profil Singkat
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px;">
                                <i class="bi bi-person text-white" style="font-size: 3rem;"></i>
                            </div>
                            <h5 class="mt-3 mb-0"><?= $data['nama_lengkap'] ?></h5>
                            <div class="text-muted">Kode: <?= $data['kode'] ?></div>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-2">
                            <div class="d-flex">
                                <span class="me-2"><i class="bi bi-telephone"></i></span>
                                <span><?= $data['no_hp'] ?></span>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="d-flex">
                                <span class="me-2"><i class="bi bi-envelope"></i></span>
                                <span><?= $data['email'] ?></span>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="d-flex">
                                <span class="me-2"><i class="bi bi-credit-card"></i></span>
                                <span><?= $data['kartu_id'] ?>: <?= $data['no_kartu_id'] ?></span>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="d-flex">
                                <span class="me-2"><i class="bi bi-file-text"></i></span>
                                <span>
                                    <?php if ($data['scan_ktp']) : ?>
                                        <a href="uploads/<?= $data['scan_ktp'] ?>" class="file-link" target="_blank">
                                            Lihat KTP
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Tidak tersedia</span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Financial Info Card -->
                <div class="card">
                    <div class="card-header">
                        <i class="bi bi-cash-stack me-2"></i>Informasi Keuangan
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Gaji Bulanan:</span>
                            <span class="fw-bold text-success">Rp <?= number_format($data['gaji'], 2, ',', '.') ?></span>
                        </div>
                        
                        <div class="progress mb-3" style="height: 10px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 75%;" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        
                        <div class="alert alert-info py-2">
                            <small>
                                <i class="bi bi-info-circle me-1"></i> 
                                Data keuangan digunakan untuk analisis kelayakan kredit.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-8">
                <!-- Main Details Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-person-lines-fill me-2"></i>Detail Lengkap
                    </div>
                    <div class="card-body">
                        <h5 class="section-divider">Informasi Pribadi</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="info-label">NPWP</div>
                                    <div class="info-value"><?= $data['npwp'] ?: '-' ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="info-label">Status Pasangan</div>
                                    <div class="info-value"><?= $data['status_pasangan'] ?: '-' ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="info-label">Alamat Lengkap</div>
                            <div class="info-value"><?= nl2br($data['alamat_lengkap']) ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="info-label">Keterangan/Boking</div>
                            <div class="info-value"><?= nl2br($data['ket_boking']) ?: '-' ?></div>
                        </div>
                        
                        <h5 class="section-divider">Informasi Pekerjaan</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="info-label">Pekerjaan</div>
                                    <div class="info-value"><?= $data['pekerjaan'] ?: '-' ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="info-label">Nama Kantor</div>
                                    <div class="info-value"><?= $data['nama_kantor'] ?: '-' ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="info-label">Alamat Kantor</div>
                            <div class="info-value"><?= nl2br($data['alamat_kantor']) ?: '-' ?></div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="info-label">Telepon Kantor</div>
                                    <div class="info-value"><?= $data['telp_kantor'] ?: '-' ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="info-label">Keterangan Kerja</div>
                                    <div class="info-value"><?= nl2br($data['ket_kerja']) ?: '-' ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <h5 class="section-divider">Informasi Pasangan</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="info-label">Nama Pasangan</div>
                                    <div class="info-value"><?= $data['nama_pasangan'] ?: '-' ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="info-label">HP Pasangan</div>
                                    <div class="info-value"><?= $data['hp_pasangan'] ?: '-' ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="info-label">Pekerjaan Pasangan</div>
                            <div class="info-value"><?= $data['kerja_pasangan'] ?: '-' ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="info-label">Alamat Kantor/Usaha Pasangan</div>
                            <div class="info-value"><?= nl2br($data['alamat_kerja_pasangan']) ?: '-' ?></div>
                        </div>
                        
                        <div class="mb-3">
                            <div class="info-label">Keterangan Pasangan</div>
                            <div class="info-value"><?= nl2br($data['ket_pasangan']) ?: '-' ?></div>
                        </div>
                        
                        <h5 class="section-divider">Informasi Keluarga</h5>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="info-label">Nama Keluarga</div>
                                    <div class="info-value"><?= $data['nama_keluarga'] ?: '-' ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="info-label">Hubungan</div>
                                    <div class="info-value"><?= $data['hubungan_keluarga'] ?: '-' ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="info-label">Telp/HP Keluarga</div>
                                    <div class="info-value"><?= $data['telp_keluarga'] ?: '-' ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-2">
                                    <div class="info-label">Alamat Keluarga</div>
                                    <div class="info-value"><?= nl2br($data['alamat_keluarga']) ?: '-' ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-outline-primary">
                        <i class="bi bi-pencil-square me-1"></i> Edit Data
                    </button>
                    <button class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i> Approve
                    </button>
                    <button class="btn btn-danger">
                        <i class="bi bi-x-circle me-1"></i> Reject
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>