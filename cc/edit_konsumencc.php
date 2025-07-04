<?php
$conn = new mysqli("srv143.niagahoster.com", "n1572337_developer", "bad_cookies8080", "n1572337_developer");
$kode = $_GET['kode'];

$result = $conn->query("SELECT * FROM calon_konsumen WHERE kode = '$kode'");
$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect all form data
    $updateData = [
        'npwp' => $_POST['npwp'],
        'nama_lengkap' => $_POST['nama_lengkap'],
        'no_hp' => $_POST['no_hp'],
        'kartu_id' => $_POST['kartu_id'],
        'no_kartu_id' => $_POST['no_kartu_id'],
        'alamat_lengkap' => $_POST['alamat_lengkap'],
        'ket_boking' => $_POST['ket_boking'],
        'email' => $_POST['email'],
        'gaji' => $_POST['gaji'],
        'pekerjaan' => $_POST['pekerjaan'],
        'nama_kantor' => $_POST['nama_kantor'],
        'alamat_kantor' => $_POST['alamat_kantor'],
        'telp_kantor' => $_POST['telp_kantor'],
        'ket_kerja' => $_POST['ket_kerja'],
        'status_pasangan' => $_POST['status_pasangan'],
        'nama_pasangan' => $_POST['nama_pasangan'],
        'hp_pasangan' => $_POST['hp_pasangan'],
        'kerja_pasangan' => $_POST['kerja_pasangan'],
        'alamat_kerja_pasangan' => $_POST['alamat_kerja_pasangan'],
        'ket_pasangan' => $_POST['ket_pasangan'],
        'nama_keluarga' => $_POST['nama_keluarga'],
        'hubungan_keluarga' => $_POST['hubungan_keluarga'],
        'telp_keluarga' => $_POST['telp_keluarga'],
        'alamat_keluarga' => $_POST['alamat_keluarga']
    ];
    
    // Escape all values to prevent SQL injection
    $escapedData = array_map([$conn, 'real_escape_string'], $updateData);
    
    // Build the UPDATE query
    $query = "UPDATE calon_konsumen SET 
              npwp = '{$escapedData['npwp']}',
              nama_lengkap = '{$escapedData['nama_lengkap']}',
              no_hp = '{$escapedData['no_hp']}',
              kartu_id = '{$escapedData['kartu_id']}',
              no_kartu_id = '{$escapedData['no_kartu_id']}',
              alamat_lengkap = '{$escapedData['alamat_lengkap']}',
              ket_boking = '{$escapedData['ket_boking']}',
              email = '{$escapedData['email']}',
              gaji = '{$escapedData['gaji']}',
              pekerjaan = '{$escapedData['pekerjaan']}',
              nama_kantor = '{$escapedData['nama_kantor']}',
              alamat_kantor = '{$escapedData['alamat_kantor']}',
              telp_kantor = '{$escapedData['telp_kantor']}',
              ket_kerja = '{$escapedData['ket_kerja']}',
              status_pasangan = '{$escapedData['status_pasangan']}',
              nama_pasangan = '{$escapedData['nama_pasangan']}',
              hp_pasangan = '{$escapedData['hp_pasangan']}',
              kerja_pasangan = '{$escapedData['kerja_pasangan']}',
              alamat_kerja_pasangan = '{$escapedData['alamat_kerja_pasangan']}',
              ket_pasangan = '{$escapedData['ket_pasangan']}',
              nama_keluarga = '{$escapedData['nama_keluarga']}',
              hubungan_keluarga = '{$escapedData['hubungan_keluarga']}',
              telp_keluarga = '{$escapedData['telp_keluarga']}',
              alamat_keluarga = '{$escapedData['alamat_keluarga']}'
              WHERE kode = '$kode'";
    
    if ($conn->query($query)) {
        header("Location: detail_konsumen.php?kode=$kode");
        exit;
    } else {
        $error = "Error updating record: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Konsumen Lengkap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .form-container {
            max-width: 1200px;
            margin: 30px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
            padding: 30px;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 30px;
            color: var(--dark-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 20px;
        }
        
        .form-header h2 {
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .form-header p {
            color: #6c757d;
        }
        
        .section-header {
            background-color: var(--primary-color);
            color: white;
            padding: 10px 15px;
            border-radius: 8px;
            margin: 25px 0 15px;
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .section-header i {
            margin-right: 10px;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--dark-color);
        }
        
        .form-control, .form-select {
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ced4da;
            transition: all 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-submit:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .btn-cancel {
            background-color: #6c757d;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-cancel:hover {
            background-color: #5a6268;
            color: white;
        }
        
        .input-group-text {
            background-color: #e9ecef;
            border-radius: 8px 0 0 8px;
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        
        .customer-code {
            background-color: #e3f2fd;
            color: var(--primary-color);
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 500;
            display: inline-block;
            margin-top: 10px;
        }
        
        .file-preview {
            margin-top: 10px;
        }
        
        .file-preview a {
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .file-preview a:hover {
            text-decoration: underline;
        }
        
        @media (max-width: 768px) {
            .form-container {
                margin: 15px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-user-edit me-2"></i>Edit Data Konsumen Lengkap</h2>
            <p>Perbarui semua informasi konsumen di bawah ini</p>
            <div class="customer-code">
                <i class="fas fa-id-card me-2"></i>Kode Konsumen: <?= htmlspecialchars($kode) ?>
            </div>
        </div>
        
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <input type="hidden" name="kode" value="<?= $kode ?>">
            
            <!-- Personal Information Section -->
            <div class="section-header">
                <i class="fas fa-user"></i>Informasi Pribadi
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_lengkap" class="form-label required-field">Nama Lengkap</label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" 
                           value="<?= htmlspecialchars($data['nama_lengkap']) ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="npwp" class="form-label">NPWP</label>
                    <input type="text" class="form-control" id="npwp" name="npwp" 
                           value="<?= htmlspecialchars($data['npwp']) ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="no_hp" class="form-label required-field">No. HP</label>
                    <input type="tel" class="form-control" id="no_hp" name="no_hp" 
                           value="<?= htmlspecialchars($data['no_hp']) ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?= htmlspecialchars($data['email']) ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="kartu_id" class="form-label required-field">Jenis Kartu ID</label>
                    <select class="form-select" id="kartu_id" name="kartu_id" required>
                        <option value="KTP" <?= $data['kartu_id'] == 'KTP' ? 'selected' : '' ?>>KTP</option>
                        <option value="SIM" <?= $data['kartu_id'] == 'SIM' ? 'selected' : '' ?>>SIM</option>
                        <option value="Passport" <?= $data['kartu_id'] == 'Passport' ? 'selected' : '' ?>>Passport</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="no_kartu_id" class="form-label required-field">Nomor Kartu ID</label>
                    <input type="text" class="form-control" id="no_kartu_id" name="no_kartu_id" 
                           value="<?= htmlspecialchars($data['no_kartu_id']) ?>" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Scan KTP</label>
                    <div class="file-preview">
                        <?php if ($data['scan_ktp']): ?>
                            <a href="uploads/<?= $data['scan_ktp'] ?>" target="_blank">
                                <i class="fas fa-file-pdf"></i> Lihat File
                            </a>
                        <?php else: ?>
                            <span class="text-muted">Tidak ada file</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="alamat_lengkap" class="form-label required-field">Alamat Lengkap</label>
                <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" 
                          required><?= htmlspecialchars($data['alamat_lengkap']) ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="ket_boking" class="form-label">Keterangan/Boking</label>
                <textarea class="form-control" id="ket_boking" name="ket_boking"><?= htmlspecialchars($data['ket_boking']) ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="gaji" class="form-label required-field">Gaji (Rp)</label>
                <input type="number" class="form-control" id="gaji" name="gaji" step="0.01" 
                       value="<?= htmlspecialchars($data['gaji']) ?>" required>
            </div>
            
            <!-- Work Information Section -->
            <div class="section-header">
                <i class="fas fa-briefcase"></i>Informasi Pekerjaan
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="pekerjaan" class="form-label">Pekerjaan</label>
                    <input type="text" class="form-control" id="pekerjaan" name="pekerjaan" 
                           value="<?= htmlspecialchars($data['pekerjaan']) ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="nama_kantor" class="form-label">Nama Kantor</label>
                    <input type="text" class="form-control" id="nama_kantor" name="nama_kantor" 
                           value="<?= htmlspecialchars($data['nama_kantor']) ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="alamat_kantor" class="form-label">Alamat Kantor</label>
                <textarea class="form-control" id="alamat_kantor" name="alamat_kantor"><?= htmlspecialchars($data['alamat_kantor']) ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="telp_kantor" class="form-label">Telepon Kantor</label>
                    <input type="text" class="form-control" id="telp_kantor" name="telp_kantor" 
                           value="<?= htmlspecialchars($data['telp_kantor']) ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="ket_kerja" class="form-label">Keterangan Kerja</label>
                    <input type="text" class="form-control" id="ket_kerja" name="ket_kerja" 
                           value="<?= htmlspecialchars($data['ket_kerja']) ?>">
                </div>
            </div>
            
            <!-- Spouse Information Section -->
            <div class="section-header">
                <i class="fas fa-user-friends"></i>Informasi Pasangan
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="status_pasangan" class="form-label">Status Pasangan</label>
                    <select class="form-select" id="status_pasangan" name="status_pasangan">
                        <option value="">Pilih Status</option>
                        <option value="Menikah" <?= $data['status_pasangan'] == 'Menikah' ? 'selected' : '' ?>>Menikah</option>
                        <option value="Belum Menikah" <?= $data['status_pasangan'] == 'Belum Menikah' ? 'selected' : '' ?>>Belum Menikah</option>
                        <option value="Cerai" <?= $data['status_pasangan'] == 'Cerai' ? 'selected' : '' ?>>Cerai</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="nama_pasangan" class="form-label">Nama Pasangan</label>
                    <input type="text" class="form-control" id="nama_pasangan" name="nama_pasangan" 
                           value="<?= htmlspecialchars($data['nama_pasangan']) ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label for="hp_pasangan" class="form-label">HP Pasangan</label>
                    <input type="text" class="form-control" id="hp_pasangan" name="hp_pasangan" 
                           value="<?= htmlspecialchars($data['hp_pasangan']) ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="kerja_pasangan" class="form-label">Pekerjaan Pasangan</label>
                    <input type="text" class="form-control" id="kerja_pasangan" name="kerja_pasangan" 
                           value="<?= htmlspecialchars($data['kerja_pasangan']) ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="alamat_kerja_pasangan" class="form-label">Alamat Kantor/Usaha Pasangan</label>
                    <input type="text" class="form-control" id="alamat_kerja_pasangan" name="alamat_kerja_pasangan" 
                           value="<?= htmlspecialchars($data['alamat_kerja_pasangan']) ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label for="ket_pasangan" class="form-label">Keterangan Pasangan</label>
                <textarea class="form-control" id="ket_pasangan" name="ket_pasangan"><?= htmlspecialchars($data['ket_pasangan']) ?></textarea>
            </div>
            
            <!-- Family Information Section -->
            <div class="section-header">
                <i class="fas fa-users"></i>Informasi Keluarga
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="nama_keluarga" class="form-label">Nama Keluarga</label>
                    <input type="text" class="form-control" id="nama_keluarga" name="nama_keluarga" 
                           value="<?= htmlspecialchars($data['nama_keluarga']) ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="hubungan_keluarga" class="form-label">Hubungan dengan Anda</label>
                    <input type="text" class="form-control" id="hubungan_keluarga" name="hubungan_keluarga" 
                           value="<?= htmlspecialchars($data['hubungan_keluarga']) ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="telp_keluarga" class="form-label">Telp/HP Keluarga</label>
                    <input type="text" class="form-control" id="telp_keluarga" name="telp_keluarga" 
                           value="<?= htmlspecialchars($data['telp_keluarga']) ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="alamat_keluarga" class="form-label">Alamat Keluarga</label>
                    <input type="text" class="form-control" id="alamat_keluarga" name="alamat_keluarga" 
                           value="<?= htmlspecialchars($data['alamat_keluarga']) ?>">
                </div>
            </div>
            
            <div class="d-flex justify-content-between mt-4">
                <a href="daftar_konsumen.php" class="btn btn-cancel">
                    <i class="fas fa-times me-2"></i>Batal
                </a>
                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-save me-2"></i>Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Format phone number input
        document.getElementById('no_hp').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        // Format currency for salary
        document.getElementById('gaji').addEventListener('focus', function(e) {
            this.value = parseFloat(this.value).toFixed(2);
        });
        
        document.getElementById('gaji').addEventListener('blur', function(e) {
            this.value = parseFloat(this.value).toLocaleString('id-ID', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        });
    </script>
</body>
</html>