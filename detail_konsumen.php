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

$kode = $_GET['kode'];
$result = $conn->query("SELECT * FROM calon_konsumen WHERE kode = '$kode'");
$data = $result->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Detail Calon Konsumen - <?= $data['nama_lengkap'] ?></title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js" defer></script>
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
    <div id="navbar" class=" rounded-md fixed top-0 left-0 w-full z-10 transition-all duration-300 ease-out ml-64">
      <?php include 'templates/navbar.php'; ?>
    </div>

    <!-- Content -->
<div class="max-w-7xl mx-auto px-4 py-6 mt-16">
    <!-- Header Section -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <svg class="w-6 h-6 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
            </svg>
            Detail Calon Konsumen
        </h1>
        <a href="daftar_konsumen.php" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2.5 inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"></path>
            </svg>
            Kembali ke Daftar
        </a>
    </div>
    
    <!-- Success Message -->
    <?php if (!empty($loginSuccess)): ?>
    <div class="p-4 mb-6 text-sm text-green-700 bg-green-100 rounded-lg">
        <?= $loginSuccess ?>
    </div>
    <?php endif; ?>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Left Column -->
        <div class="md:col-span-1 space-y-6">
            <!-- Profile Summary Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                        Profil Singkat
                    </h2>
                </div>
                
                <div class="text-center mb-4">
                    <div class="bg-gray-200 rounded-full w-24 h-24 flex items-center justify-center mx-auto">
                        <svg class="w-12 h-12 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <h3 class="mt-3 text-lg font-medium text-gray-900"><?= $data['nama_lengkap'] ?></h3>
                    <p class="text-sm text-gray-500">Kode: <?= $data['kode'] ?></p>
                </div>
                
                <div class="space-y-3">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-gray-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"></path>
                        </svg>
                        <span><?= $data['no_hp'] ?></span>
                    </div>
                    
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-gray-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"></path>
                            <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"></path>
                        </svg>
                        <span><?= $data['email'] ?></span>
                    </div>
                    
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-gray-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 00-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd"></path>
                        </svg>
                        <span><?= $data['kartu_id'] ?>: <?= $data['no_kartu_id'] ?></span>
                    </div>
                    
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-gray-500 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                        </svg>
                        <span>
                            <?php if ($data['scan_ktp']) : ?>
                                <a href="uploads/<?= $data['scan_ktp'] ?>" class="text-blue-600 hover:underline" target="_blank">
                                    Lihat KTP
                                </a>
                            <?php else: ?>
                                <span class="text-gray-500">Tidak tersedia</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Financial Info Card -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-4">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                        </svg>
                        Informasi Keuangan
                    </h2>
                </div>
                
                <div class="flex justify-between items-center mb-3">
                    <span class="text-gray-700">Gaji Bulanan:</span>
                    <span class="font-bold text-green-600">Rp <?= number_format($data['gaji'], 2, ',', '.') ?></span>
                </div>
                
                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-4">
                    <div class="bg-green-600 h-2.5 rounded-full" style="width: 75%"></div>
                </div>
                
                <div class="p-4 mb-4 text-sm text-blue-700 bg-blue-100 rounded-lg">
                    <svg class="inline w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="font-medium">Info:</span> Data keuangan digunakan untuk analisis kelayakan kredit.
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
<div class="md:col-span-3 space-y-6">
    <!-- Main Details Card -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between border-b border-gray-200 pb-4 mb-6">
            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v1h-3zM4.75 12.094A5.973 5.973 0 004 15v1H1v-1a3 3 0 013.75-2.906z"></path>
                </svg>
                Detail Lengkap
            </h2>
        </div>
        
        <!-- Personal Information Card -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 pb-2 mb-4 border-b border-gray-200 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
                Informasi Pribadi
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">NPWP</p>
                    <p class="text-gray-900"><?= $data['npwp'] ?: '-' ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Status Pasangan</p>
                    <p class="text-gray-900"><?= $data['status_pasangan'] ?: '-' ?></p>
                </div>
            </div>
            
            <div class="mb-4">
                <p class="text-sm font-medium text-gray-500">Alamat Lengkap</p>
                <p class="text-gray-900 whitespace-pre-line"><?= $data['alamat_lengkap'] ?></p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-500">Keterangan/Boking</p>
                <p class="text-gray-900 whitespace-pre-line"><?= $data['ket_boking'] ?: '-' ?></p>
            </div>
        </div>
        
        <!-- Work Information Card -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 pb-2 mb-4 border-b border-gray-200 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    <path d="M2 13.692V16a2 2 0 002 2h12a2 2 0 002-2v-2.308A24.974 24.974 0 0110 15c-2.796 0-5.487-.46-8-1.308z"></path>
                </svg>
                Informasi Pekerjaan
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Pekerjaan</p>
                    <p class="text-gray-900"><?= $data['pekerjaan'] ?: '-' ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Nama Kantor</p>
                    <p class="text-gray-900"><?= $data['nama_kantor'] ?: '-' ?></p>
                </div>
            </div>
            
            <div class="mb-4">
                <p class="text-sm font-medium text-gray-500">Alamat Kantor</p>
                <p class="text-gray-900 whitespace-pre-line"><?= $data['alamat_kantor'] ?: '-' ?></p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Telepon Kantor</p>
                    <p class="text-gray-900"><?= $data['telp_kantor'] ?: '-' ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Keterangan Kerja</p>
                    <p class="text-gray-900 whitespace-pre-line"><?= $data['ket_kerja'] ?: '-' ?></p>
                </div>
            </div>
        </div>
        
        <!-- Spouse Information Card -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 pb-2 mb-4 border-b border-gray-200 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v1h-3zM4.75 12.094A5.973 5.973 0 004 15v1H1v-1a3 3 0 013.75-2.906z"></path>
                </svg>
                Informasi Pasangan
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Nama Pasangan</p>
                    <p class="text-gray-900"><?= $data['nama_pasangan'] ?: '-' ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">HP Pasangan</p>
                    <p class="text-gray-900"><?= $data['hp_pasangan'] ?: '-' ?></p>
                </div>
            </div>
            
            <div class="mb-4">
                <p class="text-sm font-medium text-gray-500">Pekerjaan Pasangan</p>
                <p class="text-gray-900"><?= $data['kerja_pasangan'] ?: '-' ?></p>
            </div>
            
            <div class="mb-4">
                <p class="text-sm font-medium text-gray-500">Alamat Kantor/Usaha Pasangan</p>
                <p class="text-gray-900 whitespace-pre-line"><?= $data['alamat_kerja_pasangan'] ?: '-' ?></p>
            </div>
            
            <div>
                <p class="text-sm font-medium text-gray-500">Keterangan Pasangan</p>
                <p class="text-gray-900 whitespace-pre-line"><?= $data['ket_pasangan'] ?: '-' ?></p>
            </div>
        </div>
        
        <!-- Family Information Card -->
        <div class="p-4 bg-gray-50 rounded-lg border border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 pb-2 mb-4 border-b border-gray-200 flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-600" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z"></path>
                </svg>
                Informasi Keluarga
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Nama Keluarga</p>
                    <p class="text-gray-900"><?= $data['nama_keluarga'] ?: '-' ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Hubungan</p>
                    <p class="text-gray-900"><?= $data['hubungan_keluarga'] ?: '-' ?></p>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm font-medium text-gray-500">Telp/HP Keluarga</p>
                    <p class="text-gray-900"><?= $data['telp_keluarga'] ?: '-' ?></p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-500">Alamat Keluarga</p>
                    <p class="text-gray-900 whitespace-pre-line"><?= $data['alamat_keluarga'] ?: '-' ?></p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons -->
    <div class="flex justify-end space-x-3">
        <a href="edit_konsumen.php?kode=<?= $data['kode'] ?>" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
            </svg>
            Edit Data
        </a>
        <button type="button" class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
            </svg>
            Approve
        </button>
        <button type="button" class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
            </svg>
            Reject
        </button>
    </div>
</div>

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