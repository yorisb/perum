<?php
$conn = new mysqli("srv143.niagahoster.com", "n1572337_developer", "bad_cookies8080", "n1572337_developer");

// Generate kode otomatis
$result = $conn->query("SELECT MAX(kode) as kode_terakhir FROM calon_konsumen");
$row = $result->fetch_assoc();
$kode_terakhir = $row['kode_terakhir'];

if ($kode_terakhir) {
    $angka = (int) substr($kode_terakhir, 1);
    $angka_baru = $angka + 1;
    $kode_baru = 'K' . str_pad($angka_baru, 6, '0', STR_PAD_LEFT);
} else {
    $kode_baru = 'K000001';
}

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $kode               = $_POST['kode'];
    $npwp               = $_POST['npwp'];
    $nama_lengkap       = $_POST['nama_lengkap'];
    $no_hp              = $_POST['no_hp'];
    $kartu_id           = $_POST['kartu_id'];
    $no_kartu_id        = $_POST['no_kartu_id'];
    $alamat_lengkap     = $_POST['alamat_lengkap'];
    $ket_boking         = $_POST['ket_boking'];
    $email              = $_POST['email'];
    $gaji               = $_POST['gaji'];

    $pekerjaan          = $_POST['pekerjaan'];
    $nama_kantor        = $_POST['nama_kantor'];
    $alamat_kantor      = $_POST['alamat_kantor'];
    $telp_kantor        = $_POST['telp_kantor'];
    $ket_kerja          = $_POST['ket_kerja'];

    $status_pasangan    = $_POST['status_pasangan'];
    $nama_pasangan      = $_POST['nama_pasangan'];
    $hp_pasangan        = $_POST['hp_pasangan'];
    $kerja_pasangan     = $_POST['kerja_pasangan'];
    $alamat_kerja_pasangan = $_POST['alamat_kerja_pasangan'];
    $ket_pasangan       = $_POST['ket_pasangan'];

    $nama_keluarga      = $_POST['nama_keluarga'];
    $hubungan_keluarga  = $_POST['hubungan_keluarga'];
    $telp_keluarga      = $_POST['telp_keluarga'];
    $alamat_keluarga    = $_POST['alamat_keluarga'];

    // Upload KTP
    $scan_ktp = '';
    if (isset($_FILES['scan_ktp']) && $_FILES['scan_ktp']['error'] == 0) {
        $scan_ktp = 'uploads/' . basename($_FILES['scan_ktp']['name']);
        move_uploaded_file($_FILES['scan_ktp']['tmp_name'], $scan_ktp);
    }

    $sql = "INSERT INTO calon_konsumen (
        kode, npwp, nama_lengkap, no_hp, kartu_id, no_kartu_id, scan_ktp, alamat_lengkap, ket_boking, email, gaji,
        pekerjaan, nama_kantor, alamat_kantor, telp_kantor, ket_kerja,
        status_pasangan, nama_pasangan, hp_pasangan, kerja_pasangan, alamat_kerja_pasangan, ket_pasangan,
        nama_keluarga, hubungan_keluarga, telp_keluarga, alamat_keluarga
    ) VALUES (
        '$kode', '$npwp', '$nama_lengkap', '$no_hp', '$kartu_id', '$no_kartu_id', '$scan_ktp', '$alamat_lengkap', '$ket_boking', '$email', '$gaji',
        '$pekerjaan', '$nama_kantor', '$alamat_kantor', '$telp_kantor', '$ket_kerja',
        '$status_pasangan', '$nama_pasangan', '$hp_pasangan', '$kerja_pasangan', '$alamat_kerja_pasangan', '$ket_pasangan',
        '$nama_keluarga', '$hubungan_keluarga', '$telp_keluarga', '$alamat_keluarga'
    )";

    if ($conn->query($sql) === TRUE) {
        echo "<p style='color:green;'>Data berhasil disimpan!</p>";
    } else {
        echo "<p style='color:red;'>Error: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            padding: 20px;
        }
        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .form-section h3 {
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3498db;
        }
        .required-field::after {
            content: " *";
            color: red;
        }
        .btn-submit {
            background-color: #3498db;
            color: white;
            padding: 10px 25px;
            font-weight: bold;
        }
        .btn-submit:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="form-container">
                    <h2 class="text-center mb-4">Customer Registration Form</h2>
                    
                    <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($conn->error)): ?>
                        <div class="alert alert-danger"><?= $conn->error ?></div>
                    <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
                        <div class="alert alert-success">Data berhasil disimpan!</div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                        <!-- Personal Data Section -->
                        <div class="form-section">
                            <h3>Personal Information</h3>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="kode" class="form-label">Customer Code</label>
                                    <input type="text" class="form-control" id="kode" name="kode" value="<?= $kode_baru ?>" readonly>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="npwp" class="form-label">NPWP</label>
                                    <input type="text" class="form-control" id="npwp" name="npwp">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama_lengkap" class="form-label required-field">Full Name</label>
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="no_hp" class="form-label required-field">Phone Number</label>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="kartu_id" class="form-label required-field">ID Type</label>
                                    <select class="form-select" id="kartu_id" name="kartu_id" required>
                                        <option value="">-- Select --</option>
                                        <option value="KTP">KTP</option>
                                        <option value="SIM">SIM</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="no_kartu_id" class="form-label required-field">ID Number</label>
                                    <input type="text" class="form-control" id="no_kartu_id" name="no_kartu_id" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="scan_ktp" class="form-label">ID Scan</label>
                                    <input type="file" class="form-control" id="scan_ktp" name="scan_ktp">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="alamat_lengkap" class="form-label required-field">Full Address</label>
                                <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="3" required></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label required-field">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gaji" class="form-label required-field">Salary</label>
                                    <input type="number" class="form-control" id="gaji" name="gaji" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="ket_boking" class="form-label">Notes/Booking</label>
                                <textarea class="form-control" id="ket_boking" name="ket_boking" rows="2"></textarea>
                            </div>
                        </div>
                        
                        <!-- Employment Data Section -->
                        <div class="form-section">
                            <h3>Employment Information</h3>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="pekerjaan" class="form-label">Occupation</label>
                                    <select class="form-select" id="pekerjaan" name="pekerjaan">
                                        <option value="">-- Select --</option>
                                        <option value="PNS">PNS</option>
                                        <option value="Pegawai Swasta">Private Employee</option>
                                        <option value="Wiraswasta">Entrepreneur</option>
                                        <option value="Petani">Farmer</option>
                                        <option value="Nelayan">Fisherman</option>
                                        <option value="Buruh">Laborer</option>
                                        <option value="Tidak Bekerja">Not Working</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="nama_kantor" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="nama_kantor" name="nama_kantor">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="alamat_kantor" class="form-label">Company Address</label>
                                <textarea class="form-control" id="alamat_kantor" name="alamat_kantor" rows="2"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="telp_kantor" class="form-label">Company Phone</label>
                                    <input type="text" class="form-control" id="telp_kantor" name="telp_kantor">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ket_kerja" class="form-label">Work Notes</label>
                                    <input type="text" class="form-control" id="ket_kerja" name="ket_kerja">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Spouse Data Section -->
                        <div class="form-section">
                            <h3>Spouse Information</h3>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="status_pasangan" class="form-label">Marital Status</label>
                                    <select class="form-select" id="status_pasangan" name="status_pasangan">
                                        <option value="">-- Select --</option>
                                        <option value="Menikah">Married</option>
                                        <option value="Belum Menikah">Single</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="nama_pasangan" class="form-label">Spouse Name</label>
                                    <input type="text" class="form-control" id="nama_pasangan" name="nama_pasangan">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="hp_pasangan" class="form-label">Spouse Phone</label>
                                    <input type="text" class="form-control" id="hp_pasangan" name="hp_pasangan">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="kerja_pasangan" class="form-label">Spouse Occupation</label>
                                    <select class="form-select" id="kerja_pasangan" name="kerja_pasangan">
                                        <option value="">-- Select --</option>
                                        <option value="PNS">PNS</option>
                                        <option value="Pegawai Swasta">Private Employee</option>
                                        <option value="Wiraswasta">Entrepreneur</option>
                                        <option value="Petani">Farmer</option>
                                        <option value="Nelayan">Fisherman</option>
                                        <option value="Buruh">Laborer</option>
                                        <option value="Tidak Bekerja">Not Working</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="alamat_kerja_pasangan" class="form-label">Spouse Work Address</label>
                                    <input type="text" class="form-control" id="alamat_kerja_pasangan" name="alamat_kerja_pasangan">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="ket_pasangan" class="form-label">Spouse Notes</label>
                                <textarea class="form-control" id="ket_pasangan" name="ket_pasangan" rows="2"></textarea>
                            </div>
                        </div>
                        
                        <!-- Family Data Section -->
                        <div class="form-section">
                            <h3>Family Information</h3>
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="nama_keluarga" class="form-label">Family Member Name</label>
                                    <input type="text" class="form-control" id="nama_keluarga" name="nama_keluarga">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="hubungan_keluarga" class="form-label">Relationship</label>
                                    <select class="form-select" id="hubungan_keluarga" name="hubungan_keluarga">
                                        <option value="">-- Select --</option>
                                        <option value="Ayah">Father</option>
                                        <option value="Ibu">Mother</option>
                                        <option value="Saudara">Sibling</option>
                                        <option value="Teman">Friend</option>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="telp_keluarga" class="form-label">Family Phone</label>
                                    <input type="text" class="form-control" id="telp_keluarga" name="telp_keluarga">
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="alamat_keluarga" class="form-label">Family Address</label>
                                <textarea class="form-control" id="alamat_keluarga" name="alamat_keluarga" rows="2"></textarea>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-submit">Submit Form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>