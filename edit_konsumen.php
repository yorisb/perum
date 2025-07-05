<?php
session_start();

// Cek apakah user sudah login
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
        // Set success message in session
        $_SESSION['success_message'] = "Data konsumen berhasil diperbarui.";
        
        // Redirect ke daftar_konsumen.php
        header("Location: daftar_konsumen.php");
        exit;
    } else {
        $error = "Error updating record: " . $conn->error;
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Unit Perumahan - Dashboard</title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js" defer></script>
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
    <div id="navbar" class="p rounded-md fixed top-0 left-0 w-full z-10 transition-all duration-300 ease-out ml-64">
      <?php include 'templates/navbar.php'; ?>
    </div>

    <!-- Content -->
<div class="rounded-lg max-w-7xl mx-auto p-4 mt-4">
    <!-- Form Header -->
    <div class="bg-white shadow-md mt-8 rounded-lg p-6 mb-4">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
            <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path>
            </svg>
            Edit Data Konsumen Lengkap
        </h2>
        <p class="text-gray-600 mt-1">Perbarui semua informasi konsumen di bawah ini</p>
        <div class="mt-2 flex items-center text-gray-700">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z"></path>
            </svg>
            Kode Konsumen: <?= htmlspecialchars($kode) ?>
        </div>
    </div>
    
    <?php if (isset($error)): ?>
        <div class="p-4 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
            <?= $error ?>
        </div>
    <?php endif; ?>
    
    <form method="POST" class="space-y-6 mt-8">
        <input type="hidden" name="kode" value="<?= $kode ?>">
        
<!-- Personal Information Section -->
<div class="space-y-4 bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
    <h3 class="text-lg font-medium text-gray-900 flex items-center">
        <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
        </svg>
        Informasi Pribadi
    </h3>
    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="nama_lengkap" class="block mb-2 text-sm font-medium text-gray-900">Nama Lengkap <span class="text-red-600">*</span></label>
            <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?= htmlspecialchars($data['nama_lengkap']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
        </div>
        
        <div>
            <label for="npwp" class="block mb-2 text-sm font-medium text-gray-900">NPWP</label>
            <input type="text" id="npwp" name="npwp" value="<?= htmlspecialchars($data['npwp']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
    </div>
    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="no_hp" class="block mb-2 text-sm font-medium text-gray-900">No. HP <span class="text-red-600">*</span></label>
            <input type="tel" id="no_hp" name="no_hp" value="<?= htmlspecialchars($data['no_hp']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
        </div>
        
        <div>
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($data['email']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
    </div>
    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
            <label for="kartu_id" class="block mb-2 text-sm font-medium text-gray-900">Jenis Kartu ID <span class="text-red-600">*</span></label>
            <select id="kartu_id" name="kartu_id" 
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
                <option value="KTP" <?= $data['kartu_id'] == 'KTP' ? 'selected' : '' ?>>KTP</option>
                <option value="SIM" <?= $data['kartu_id'] == 'SIM' ? 'selected' : '' ?>>SIM</option>
                <option value="Passport" <?= $data['kartu_id'] == 'Passport' ? 'selected' : '' ?>>Passport</option>
            </select>
        </div>
        
        <div>
            <label for="no_kartu_id" class="block mb-2 text-sm font-medium text-gray-900">Nomor Kartu ID <span class="text-red-600">*</span></label>
            <input type="text" id="no_kartu_id" name="no_kartu_id" value="<?= htmlspecialchars($data['no_kartu_id']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
        </div>
        
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900">Scan KTP</label>
            <div class="flex items-center">
                <?php if ($data['scan_ktp']): ?>
                    <a href="uploads/<?= $data['scan_ktp'] ?>" target="_blank" class="text-blue-600 hover:underline flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                        Lihat File
                    </a>
                <?php else: ?>
                    <span class="text-gray-500">Tidak ada file</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div>
        <label for="alamat_lengkap" class="block mb-2 text-sm font-medium text-gray-900">Alamat Lengkap <span class="text-red-600">*</span></label>
        <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" 
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required><?= htmlspecialchars($data['alamat_lengkap']) ?></textarea>
    </div>
    
    <div>
        <label for="ket_boking" class="block mb-2 text-sm font-medium text-gray-900">Keterangan/Boking</label>
        <textarea id="ket_boking" name="ket_boking" rows="2" 
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"><?= htmlspecialchars($data['ket_boking']) ?></textarea>
    </div>
    
    <div>
        <label for="gaji" class="block mb-2 text-sm font-medium text-gray-900">Gaji (Rp) <span class="text-red-600">*</span></label>
        <input type="number" id="gaji" name="gaji" step="0.01" value="<?= htmlspecialchars($data['gaji']) ?>" 
               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
    </div>
</div>

<!-- Work Information Section -->
<div class="space-y-4 bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
    <h3 class="text-lg font-medium text-gray-900 flex items-center">
        <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
            <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"></path>
        </svg>
        Informasi Pekerjaan
    </h3>
    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="pekerjaan" class="block mb-2 text-sm font-medium text-gray-900">Pekerjaan</label>
            <input type="text" id="pekerjaan" name="pekerjaan" value="<?= htmlspecialchars($data['pekerjaan']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        
        <div>
            <label for="nama_kantor" class="block mb-2 text-sm font-medium text-gray-900">Nama Kantor</label>
            <input type="text" id="nama_kantor" name="nama_kantor" value="<?= htmlspecialchars($data['nama_kantor']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
    </div>
    
    <div>
        <label for="alamat_kantor" class="block mb-2 text-sm font-medium text-gray-900">Alamat Kantor</label>
        <textarea id="alamat_kantor" name="alamat_kantor" rows="2" 
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"><?= htmlspecialchars($data['alamat_kantor']) ?></textarea>
    </div>
    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="telp_kantor" class="block mb-2 text-sm font-medium text-gray-900">Telepon Kantor</label>
            <input type="text" id="telp_kantor" name="telp_kantor" value="<?= htmlspecialchars($data['telp_kantor']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        
        <div>
            <label for="ket_kerja" class="block mb-2 text-sm font-medium text-gray-900">Keterangan Kerja</label>
            <input type="text" id="ket_kerja" name="ket_kerja" value="<?= htmlspecialchars($data['ket_kerja']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
    </div>
</div>

<!-- Spouse Information Section -->
<div class="space-y-4 bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
    <h3 class="text-lg font-medium text-gray-900 flex items-center">
        <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v1h-3zM4.75 12.094A5.973 5.973 0 004 15v1H1v-1a3 3 0 013.75-2.906z"></path>
        </svg>
        Informasi Pasangan
    </h3>
    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
            <label for="status_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Status Pasangan</label>
            <select id="status_pasangan" name="status_pasangan" 
                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                <option value="">Pilih Status</option>
                <option value="Menikah" <?= $data['status_pasangan'] == 'Menikah' ? 'selected' : '' ?>>Menikah</option>
                <option value="Belum Menikah" <?= $data['status_pasangan'] == 'Belum Menikah' ? 'selected' : '' ?>>Belum Menikah</option>
                <option value="Cerai" <?= $data['status_pasangan'] == 'Cerai' ? 'selected' : '' ?>>Cerai</option>
            </select>
        </div>
        
        <div>
            <label for="nama_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Nama Pasangan</label>
            <input type="text" id="nama_pasangan" name="nama_pasangan" value="<?= htmlspecialchars($data['nama_pasangan']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        
        <div>
            <label for="hp_pasangan" class="block mb-2 text-sm font-medium text-gray-900">HP Pasangan</label>
            <input type="text" id="hp_pasangan" name="hp_pasangan" value="<?= htmlspecialchars($data['hp_pasangan']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
    </div>
    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="kerja_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Pekerjaan Pasangan</label>
            <input type="text" id="kerja_pasangan" name="kerja_pasangan" value="<?= htmlspecialchars($data['kerja_pasangan']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        
        <div>
            <label for="alamat_kerja_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Alamat Kantor/Usaha Pasangan</label>
            <input type="text" id="alamat_kerja_pasangan" name="alamat_kerja_pasangan" value="<?= htmlspecialchars($data['alamat_kerja_pasangan']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
    </div>
    
    <div>
        <label for="ket_pasangan" class="block mb-2 text-sm font-medium text-gray-900">Keterangan Pasangan</label>
        <textarea id="ket_pasangan" name="ket_pasangan" rows="2" 
                  class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"><?= htmlspecialchars($data['ket_pasangan']) ?></textarea>
    </div>
</div>

<!-- Family Information Section -->
<div class="space-y-4 bg-white p-6 rounded-lg shadow-md border border-gray-200">
    <h3 class="text-lg font-medium text-gray-900 flex items-center">
        <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
        </svg>
        Informasi Keluarga
    </h3>
    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="nama_keluarga" class="block mb-2 text-sm font-medium text-gray-900">Nama Keluarga</label>
            <input type="text" id="nama_keluarga" name="nama_keluarga" value="<?= htmlspecialchars($data['nama_keluarga']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        
        <div>
            <label for="hubungan_keluarga" class="block mb-2 text-sm font-medium text-gray-900">Hubungan dengan Anda</label>
            <input type="text" id="hubungan_keluarga" name="hubungan_keluarga" value="<?= htmlspecialchars($data['hubungan_keluarga']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
    </div>
    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="telp_keluarga" class="block mb-2 text-sm font-medium text-gray-900">Telp/HP Keluarga</label>
            <input type="text" id="telp_keluarga" name="telp_keluarga" value="<?= htmlspecialchars($data['telp_keluarga']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        
        <div>
            <label for="alamat_keluarga" class="block mb-2 text-sm font-medium text-gray-900">Alamat Keluarga</label>
            <input type="text" id="alamat_keluarga" name="alamat_keluarga" value="<?= htmlspecialchars($data['alamat_keluarga']) ?>" 
                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
    </div>
</div>

<!-- Document Information Section -->
<div class="space-y-4 bg-white p-6 rounded-lg shadow-md border border-gray-200 mb-6">
    <h3 class="text-lg font-medium text-gray-900 flex items-center">
        <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
        </svg>
        Dokumen Pendukung
    </h3>
    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900">Scan KTP</label>
            <div class="flex items-center">
                <?php if (!empty($data['scan_ktp'])): ?>
                    <a href="<?= htmlspecialchars($data['scan_ktp']) ?>" target="_blank" class="text-blue-600 hover:underline flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                        Lihat Dokumen
                    </a>
                <?php else: ?>
                    <span class="text-gray-500">Tidak ada dokumen</span>
                <?php endif; ?>
            </div>
        </div>
        
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900">Scan KK</label>
            <div class="flex items-center">
                <?php if (!empty($data['scan_kk'])): ?>
                    <a href="<?= htmlspecialchars($data['scan_kk']) ?>" target="_blank" class="text-blue-600 hover:underline flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                        Lihat Dokumen
                    </a>
                <?php else: ?>
                    <span class="text-gray-500">Tidak ada dokumen</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900">Scan Slip Gaji</label>
            <div class="flex items-center">
                <?php if (!empty($data['scan_slip_gaji'])): ?>
                    <a href="<?= htmlspecialchars($data['scan_slip_gaji']) ?>" target="_blank" class="text-blue-600 hover:underline flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                        Lihat Dokumen
                    </a>
                <?php else: ?>
                    <span class="text-gray-500">Tidak ada dokumen</span>
                <?php endif; ?>
            </div>
        </div>
        
        <div>
            <label class="block mb-2 text-sm font-medium text-gray-900">Scan NPWP</label>
            <div class="flex items-center">
                <?php if (!empty($data['scan_npwp'])): ?>
                    <a href="<?= htmlspecialchars($data['scan_npwp']) ?>" target="_blank" class="text-blue-600 hover:underline flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                        Lihat Dokumen
                    </a>
                <?php else: ?>
                    <span class="text-gray-500">Tidak ada dokumen</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Add file upload fields if you want to allow updates -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-4">
        <div>
            <label for="update_scan_ktp" class="block mb-2 text-sm font-medium text-gray-900">Update Scan KTP</label>
            <input type="file" id="update_scan_ktp" name="update_scan_ktp" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
        </div>
        <div>
            <label for="update_scan_kk" class="block mb-2 text-sm font-medium text-gray-900">Update Scan KK</label>
            <input type="file" id="update_scan_kk" name="update_scan_kk" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
        </div>
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label for="update_scan_slip_gaji" class="block mb-2 text-sm font-medium text-gray-900">Update Scan Slip Gaji</label>
            <input type="file" id="update_scan_slip_gaji" name="update_scan_slip_gaji" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
        </div>
        <div>
            <label for="update_scan_npwp" class="block mb-2 text-sm font-medium text-gray-900">Update Scan NPWP</label>
            <input type="file" id="update_scan_npwp" name="update_scan_npwp" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
        </div>
    </div>
</div>
        
        <!-- Form Actions -->
        <div class="flex justify-between pt-6 border-t border-gray-200">
            <a href="daftar_konsumen.php" class="text-white bg-red-500 hover:bg-red-400 border border-red-200 focus:ring-4 focus:outline-none focus:ring-red-100 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                </svg>
                Batal
            </a>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

  </div>

  <!-- Flowbite -->
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