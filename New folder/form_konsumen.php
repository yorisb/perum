<?php
$conn = new mysqli("srv143.niagahoster.com", "n1572337_developer", "bad_cookies8080", "n1572337_developer");

// Auto-generate kode
$result = $conn->query("SELECT MAX(RIGHT(kode, 6)) AS kode_terakhir FROM calon_konsumen");
$data = $result->fetch_assoc();
$kodeBaru = 'K' . str_pad(($data['kode_terakhir'] + 1), 6, '0', STR_PAD_LEFT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = $_POST['kode'];
    $nama = $_POST['nama_lengkap'];
    $kartu_id = $_POST['kartu_id'];
    $alamat = $_POST['alamat_lengkap'];
    $no_hp = $_POST['no_hp'];
    $email = $_POST['email'];

    $conn->query("INSERT INTO calon_konsumen (kode, nama_lengkap, kartu_id, alamat_lengkap, no_hp, email) 
                  VALUES ('$kode', '$nama', '$kartu_id', '$alamat', '$no_hp', '$email')");
    header("Location: daftar_konsumen.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .form-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.08);
            padding: 30px;
        }
        
        .form-header {
            text-align: center;
            margin-bottom: 30px;
            color: var(--dark-color);
        }
        
        .form-header h2 {
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .form-header p {
            color: #6c757d;
        }
        
        .form-label {
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--dark-color);
        }
        
        .form-control, .form-select {
            padding: 12px;
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
            width: 100%;
        }
        
        .btn-submit:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
        }
        
        .input-group-text {
            background-color: #e9ecef;
            border-radius: 8px 0 0 8px;
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        
        @media (max-width: 768px) {
            .form-container {
                margin: 20px;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <h2><i class="fas fa-user-plus me-2"></i>Customer Registration</h2>
            <p>Please fill in all required fields to register a new customer</p>
        </div>
        
        <form method="POST">
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="kode" class="form-label">Customer Code</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                        <input type="text" class="form-control" id="kode" name="kode" value="<?= $kodeBaru ?>" readonly>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="nama_lengkap" class="form-label required-field">Full Name</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label for="kartu_id" class="form-label required-field">ID Type</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-address-card"></i></span>
                        <select class="form-select" id="kartu_id" name="kartu_id" required>
                            <option value="">Select ID Type</option>
                            <option value="KTP">KTP</option>
                            <option value="SIM">SIM</option>
                            <option value="Passport">Passport</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label for="no_hp" class="form-label required-field">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="tel" class="form-control" id="no_hp" name="no_hp" required>
                    </div>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="alamat_lengkap" class="form-label required-field">Complete Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                    <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" required></textarea>
                </div>
            </div>
            
            <div class="mb-4">
                <label for="email" class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" class="form-control" id="email" name="email">
                </div>
            </div>
            
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-submit">
                    <i class="fas fa-save me-2"></i>Save Customer Data
                </button>
                <a href="daftar_konsumen.php" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Back to Customer List
                </a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Phone number formatting
        document.getElementById('no_hp').addEventListener('input', function(e) {
            let x = e.target.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,4})(\d{0,4})/);
            e.target.value = !x[2] ? x[1] : x[1] + '-' + x[2] + (x[3] ? '-' + x[3] : '');
        });
    </script>
</body>
</html>