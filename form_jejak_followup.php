<?php
session_start();
require_once 'koneksi.php';

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

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Unit Perumahan - Tambah Jejak Follow Up</title>
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

  <div class="max-w-7xl mx-auto bg-white rounded-lg shadow-md p-6 mt-20">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
            <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
            </svg>
            Form Follow Up Konsumen
        </h2>
        <p class="text-gray-600 mt-1">Lengkapi data follow up konsumen di bawah ini</p>
    </div>
    
    <form action="proses_followup.php" method="post">
        <!-- Customer Search Field - Improved Version -->
        <div class="mb-6 relative" id="search-container">
            <label for="search-calonkonsumen" class="block mb-2 text-sm font-medium text-gray-900">
                Calon Konsumen <span class="text-red-500">*</span>
            </label>
            <div class="relative">
                <input type="text" id="search-calonkonsumen" name="calon_konsumen_display" 
                       onkeyup="searchCalonKonsumen()" required 
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 pl-10" 
                       placeholder="Ketik nama atau kode konsumen..."
                       autocomplete="off">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <div id="calon-konsumen-list" class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg hidden max-h-60 overflow-y-auto">
                    <!-- Search results will appear here -->
                </div>
            </div>
            <input type="hidden" name="calon_konsumen" id="calon_konsumen_id">
            <p class="mt-1 text-sm text-gray-500">Mulai ketik untuk mencari konsumen</p>
        </div>
        
        <!-- Rest of the form remains the same -->
        <div class="grid md:grid-cols-2 gap-6 mb-6">
            <!-- Follow Up Date -->
            <div>
                <label for="tgl_follow_up" class="block mb-2 text-sm font-medium text-gray-900">
                    Tanggal Follow Up <span class="text-red-500">*</span>
                </label>
                <input type="date" id="tgl_follow_up" name="tgl_follow_up" required
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            
            <!-- Follow Up Method -->
            <div>
                <label for="melalui" class="block mb-2 text-sm font-medium text-gray-900">
                    Melalui <span class="text-red-500">*</span>
                </label>
                <select id="melalui" name="melalui" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                    <option value="" selected disabled>Pilih metode</option>
                    <option value="SMS">SMS</option>
                    <option value="Telp">Telepon</option>
                    <option value="WhatsApp">WhatsApp</option>
                    <option value="EMail">Email</option>
                    <option value="Tatap Muka">Tatap Muka</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
        </div>
        
        <!-- Phone Number -->
        <div class="mb-6">
            <label for="telp" class="block mb-2 text-sm font-medium text-gray-900">Nomor Telepon</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                </div>
                <input type="text" id="telp" name="telp" 
                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5" 
                       placeholder="Nomor yang dihubungi">
            </div>
            <p class="mt-1 text-sm text-gray-500">Nomor akan terisi otomatis saat memilih konsumen</p>
        </div>
        
        <!-- Description -->
        <div class="mb-6">
            <label for="keterangan" class="block mb-2 text-sm font-medium text-gray-900">
                Keterangan <span class="text-red-500">*</span>
            </label>
            <textarea id="keterangan" name="keterangan" required rows="4"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                      placeholder="Deskripsi follow up..."></textarea>
        </div>
        
        <!-- Result -->
        <div class="mb-6">
            <label for="hasil" class="block mb-2 text-sm font-medium text-gray-900">Hasil Follow Up</label>
            <textarea id="hasil" name="hasil" rows="4"
                      class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" 
                      placeholder="Hasil dari follow up..."></textarea>
        </div>
        
        <!-- Progress Status -->
        <div class="mb-6">
        <label for="status_progres" class="block mb-2 text-sm font-semibold text-gray-800">
            Status Progres Konsumen <span class="text-red-500">*</span>
        </label>
        <div class="relative">
            <select id="status_progres" name="status_progres" required
            class="block w-full appearance-none p-3 text-sm text-gray-800 border border-gray-300 rounded-xl bg-white shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition ease-in-out duration-150">
            
            <option value="" disabled selected class="text-gray-400">Pilih status progres</option>

            <!-- 🟡 Tahap Awal -->
            <option disabled class="bg-yellow-100 text-yellow-700 font-semibold">-- Tahap Awal --</option>
            <option value="Call In" class="bg-yellow-100">Call In</option>
            <option value="Survey" class="bg-yellow-100">Survey</option>
            <option value="Reserve" class="bg-yellow-100">Reserve</option>

            <!-- 🔵 Tahap Administrasi -->
            <option disabled class="bg-blue-100 text-blue-700 font-semibold">-- Tahap Administrasi --</option>
            <option value="DP" class="bg-blue-100">DP</option>
            <option value="Pemberkasan" class="bg-blue-100">Pemberkasan</option>
            <option value="Wawancara" class="bg-blue-100">Wawancara</option>
            <option value="Analisa" class="bg-blue-100">Analisa</option>
            <option value="SP3K" class="bg-blue-100">SP3K</option>

            <!-- ✅ Tahap Finalisasi -->
            <option disabled class="bg-green-100 text-green-700 font-semibold">-- Tahap Finalisasi --</option>
            <option value="Akad Kredit" class="bg-green-100">Akad Kredit</option>
            <option value="Pencairan Akad" class="bg-green-100">Pencairan Akad</option>
            <option value="Cek Fisik Bangunan" class="bg-green-100">Cek Fisik Bangunan</option>
            <option value="Request Bangun" class="bg-green-100">Request Bangun</option>
            <option value="Pencairan Topping Off" class="bg-green-100">Pencairan Topping Off</option>
            <option value="Pencairan Legalitas" class="bg-green-100">Pencairan Legalitas</option>
            <!-- ❌ Tahap Khusus -->
            <option disabled class="bg-red-100 text-red-700 font-semibold">-- Tahap Khusus --</option>
            <option value="Reject" class="bg-red-100">Reject</option>
            <option value="Komplain" class="bg-red-100">Komplain</option>
            </select>

            <!-- Icon panah -->
            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
            </div>
        </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 mt-8">
            <a href="daftar_followup.php" class="inline-flex items-center justify-center px-5 py-2.5 bg-red-500 text-white font-medium rounded-lg hover:bg-red-300 transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                Kembali
            </a>
            <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 w-full sm:w-auto text-center flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                </svg>
                Simpan Follow Up
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function searchCalonKonsumen() {
        var query = $("#search-calonkonsumen").val();
        var resultsContainer = $("#calon-konsumen-list");

        if (query.length > 2) {
            $.ajax({
                url: "search_calon_konsumen.php",
                method: "GET",
                data: { query: query },
                beforeSend: function() {
                    resultsContainer.html(`
                        <div class="p-4 text-center">
                            <svg class="inline w-5 h-5 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="ml-2 text-gray-600">Mencari...</span>
                        </div>
                    `).removeClass('hidden').addClass('block');
                },
                success: function(response) {
                    if (response.trim() !== '') {
                        resultsContainer.html(response).removeClass('hidden').addClass('block');
                    } else {
                        resultsContainer.html(`
                            <div class="p-4 text-center text-gray-500">
                                <svg class="inline w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="ml-2">Tidak ditemukan</span>
                            </div>
                        `).removeClass('hidden').addClass('block');
                    }
                },
                error: function() {
                    resultsContainer.html(`
                        <div class="p-4 text-center text-red-500">
                            <svg class="inline w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <span class="ml-2">Error loading results</span>
                        </div>
                    `).removeClass('hidden').addClass('block');
                }
            });
        } else {
            resultsContainer.addClass('hidden').removeClass('block');
        }
    }

    // Hide results when clicking elsewhere
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search-container').length) {
            $('#calon-konsumen-list').addClass('hidden').removeClass('block');
        }
    });

    // When a customer item is clicked
    $(document).on("click", ".calon-konsumen-item", function() {
        var selectedId = $(this).data("id");
        var selectedCode = $(this).data("kode");
        var selectedName = $(this).data("nama");
        var selectedPhone = $(this).data("telp");

        var displayText = selectedName + " (" + selectedCode + ")";

        // Set values to form fields
        $("#search-calonkonsumen").val(displayText);
        $("#calon_konsumen_id").val(selectedId);
        $("#telp").val(selectedPhone);
        $("#alamat_lengkap").val($(this).data("alamat"));
        
        $("#calon-konsumen-list").addClass('hidden').removeClass('block');
        
        // Auto-focus next field
        $("#tgl_follow_up").focus();
    });

    // Format phone number input
    $('#telp').on('input', function() {
        this.value = this.value.replace(/[^0-9+]/g, '');
    });
</script>

<script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>

<script>
    // Menambahkan delay 5 detik untuk menghilangkan pesan
    setTimeout(function() {
        var message = document.getElementById('success-message');
        if (message) {
            message.style.display = 'none';
        }
    }, 5000);
</script>

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
</body>
</body>
</html>
