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
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Unit Perumahan - Input Transaksi</title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js" defer></script>
  <style>
    :root {
      --primary: #4361ee;
      --primary-light: #e6e9ff;
      --secondary: #3f37c9;
      --success: #4cc9f0;
      --danger: #f72585;
      --warning: #f8961e;
      --light: #f8f9fa;
      --dark: #212529;
      --gray: #6c757d;
      --border-radius: 0.375rem;
    }
    
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    .container {
      max-width: 1200px;
      margin: 0 auto;
      background: white;
      border-radius: var(--border-radius);
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      padding: 30px;
    }
    
    h2 {
      color: var(--primary);
      margin-bottom: 25px;
      padding-bottom: 10px;
      border-bottom: 2px solid var(--primary-light);
    }
    
    .form-section {
      margin-bottom: 30px;
      padding: 20px;
      background: var(--light);
      border-radius: var(--border-radius);
    }
    
    .form-section h3 {
      color: var(--secondary);
      margin-bottom: 15px;
      font-size: 1.2rem;
    }
    
    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 20px;
    }
    
    .form-group {
      margin-bottom: 15px;
    }
    
    label {
      display: block;
      margin-bottom: 5px;
      font-weight: 500;
      color: var(--dark);
    }
    
    label.required:after {
      content: " *";
      color: var(--danger);
    }
    
    input[type="text"],
    input[type="number"],
    input[type="date"],
    select,
    textarea {
      width: 100%;
      padding: 10px 15px;
      border: 1px solid #ced4da;
      border-radius: var(--border-radius);
      font-size: 0.9rem;
      transition: border-color 0.3s;
    }
    
    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus,
    select:focus,
    textarea:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 3px var(--primary-light);
    }
    
    input[readonly] {
      background-color: #e9ecef;
      cursor: not-allowed;
    }
    
    .radio-group {
      display: flex;
      gap: 15px;
      margin-top: 5px;
    }
    
    .radio-option {
      display: flex;
      align-items: center;
      gap: 5px;
    }
    
    .radio-option input {
      margin: 0;
    }
    
    .btn {
      display: inline-block;
      padding: 10px 20px;
      background-color: var(--primary);
      color: white;
      border: none;
      border-radius: var(--border-radius);
      cursor: pointer;
      font-size: 1rem;
      font-weight: 500;
      transition: background-color 0.3s;
    }
    
    .btn:hover {
      background-color: var(--secondary);
    }
    
    .btn-block {
      display: block;
      width: 100%;
    }
    
    .select2-container--default .select2-selection--single {
      height: 42px;
      border: 1px solid #ced4da;
      border-radius: var(--border-radius);
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
      line-height: 42px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
      height: 40px;
    }
    
    .angsuran-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 15px;
      margin-top: 10px;
    }
    
    .angsuran-item {
      display: flex;
      flex-direction: column;
    }
    
    .currency-input {
      position: relative;
    }
    
    .currency-input:before {
      content: "Rp";
      position: absolute;
      left: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: var(--gray);
    }
    
    .currency-input input {
      padding-left: 35px;
    }
    
    @media (max-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr;
      }
      
      .angsuran-grid {
        grid-template-columns: 1fr;
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
    <div id="navbar" class=" rounded-md fixed top-0 left-0 w-full z-10 transition-all duration-300 ease-out ml-64">
      <?php include 'templates/navbar.php'; ?>
    </div>

    <!-- Content -->
<div class="container" style="margin-top: 80px;">
  <h2>Form Input Transaksi</h2>

  <form method="POST" action="simpan_transaksi.php">
    
    <!-- Section 1: Transaction Information -->
    <div class="form-section">
      <h3>Informasi Transaksi</h3>
      <div class="form-grid">
        <div class="form-group">
          <label for="no_transaksi" class="required">No. SPR/PPJB/Transaksi</label>
          <input type="text" id="no_transaksi" name="no_transaksi" required>
        </div>
        
        <div class="form-group">
          <label for="tgl_transaksi" class="required">Tanggal Transaksi</label>
          <input type="date" id="tgl_transaksi" name="tgl_transaksi" required>
        </div>
        
        <div class="form-group">
          <label for="konsumen" class="required">Konsumen</label>
          <select name="id_konsumen" id="konsumen" required></select>
        </div>
        
        <div class="form-group">
          <label for="kode_konsumen">Kode Konsumen</label>
          <input type="text" id="kode_konsumen" name="kode_konsumen" readonly>
        </div>
      </div>
    </div>
    
    <!-- Section 2: Property Information -->
    <div class="form-section">
      <h3>Informasi Properti</h3>
      <div class="form-grid">
        <div class="form-group">
          <label for="unit_select" class="required">Nama Unit/Kapling</label>
          <select name="id_unit" id="unit_select" required>
            <option value="">Pilih Unit</option>
            <?php
              $query = $conn->query("SELECT * FROM unit_properti");
              while ($row = $query->fetch_assoc()) {
                echo "<option 
                        value='{$row['id']}' 
                        data-nama='{$row['nama_unit']}' 
                        data-type='{$row['type']}' 
                        data-luas='{$row['luas_bangunan']}' 
                        data-harga='{$row['harga']}' 
                        data-bonus='{$row['bonus']}'>
                        {$row['nama_unit']}
                      </option>";
              }
            ?>
          </select>
          <input type="hidden" name="nama_unit">
        </div>
        
        <div class="form-group">
          <label for="type">Type</label>
          <input type="text" id="type" name="type" readonly>
        </div>
        
        <div class="form-group">
          <label for="luas_bangunan">Luas Bangunan (m²)</label>
          <input type="number" id="luas_bangunan" name="luas_bangunan" value="0" readonly>
        </div>
        
        <div class="form-group">
          <label for="harga">Harga</label>
          <div class="currency-input">
            <input type="number" id="harga" name="harga" readonly>
          </div>
        </div>
        
        <div class="form-group">
          <label for="bonus">Bonus/Keterangan</label>
          <input type="text" id="bonus" name="bonus" readonly>
        </div>
      </div>
    </div>
    
    <!-- Section 3: Additional Information -->
    <div class="form-section">
      <h3>Penambahan dan Kesepakatan</h3>
      <div class="form-grid">
        <div class="form-group">
          <label for="harga_kesepakatan">Harga Kesepakatan</label>
          <div class="currency-input">
            <input type="number" id="harga_kesepakatan" name="harga_kesepakatan" value="0" oninput="updatePenambahan()">
          </div>
        </div>
        
        <div class="form-group">
          <label for="luas_tanah">Luas Tanah (m²)</label>
          <input type="number" id="luas_tanah" name="luas_tanah" value="0" oninput="updatePenambahan()">
        </div>
        
        <div class="form-group">
          <label for="harga_per_m2">Harga per m²</label>
          <div class="currency-input">
            <input type="number" id="harga_per_m2" name="harga_per_m2" value="0" oninput="updatePenambahan()">
          </div>
        </div>
        
        <div class="form-group">
          <label for="total_harga_penambahan">Total Harga Penambahan Tanah</label>
          <div class="currency-input">
            <input type="number" id="total_harga_penambahan" name="total_harga_penambahan" value="0" readonly>
          </div>
        </div>
        
        <div class="form-group">
          <label for="penambahan_lain">Penambahan Lain</label>
          <input type="text" id="penambahan_lain" name="penambahan_lain">
        </div>
        
        <div class="form-group">
          <label for="total_harga_penambahan_lain">Total Harga Penambahan Lain</label>
          <div class="currency-input">
            <input type="number" id="total_harga_penambahan_lain" name="total_harga_penambahan_lain" value="0" oninput="updatePenambahan()">
          </div>
        </div>
        
        <div class="form-group">
          <label for="total_penambahan">Total Penambahan</label>
          <div class="currency-input">
            <input type="number" id="total_penambahan" name="total_penambahan" value="0" readonly>
          </div>
        </div>
        
        <div class="form-group">
          <label for="total_setelah_penambahan">Total Setelah Penambahan</label>
          <div class="currency-input">
            <input type="number" id="total_setelah_penambahan" name="total_setelah_penambahan" value="0" readonly>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Section 4: Payment Information -->
    <div class="form-section">
      <h3>Informasi Pembayaran</h3>
      <div class="form-grid">
        <div class="form-group">
          <label for="tanda_jadi">Tanda Jadi</label>
          <div class="currency-input">
            <input type="number" id="tanda_jadi" name="tanda_jadi" value="0" oninput="updateUangMuka()">
          </div>
        </div>
        
        <div class="form-group">
          <label>Status Tanda Jadi</label>
          <div class="radio-group">
            <div class="radio-option">
              <input type="radio" id="tidak_masuk" name="status_tanda_jadi" value="tidak_masuk" checked onclick="updateUangMuka()">
              <label for="tidak_masuk">Tidak Masuk Harga Jual</label>
            </div>
            <div class="radio-option">
              <input type="radio" id="masuk" name="status_tanda_jadi" value="masuk" onclick="updateUangMuka()">
              <label for="masuk">Masuk Harga Jual</label>
            </div>
          </div>
        </div>
        
        <div class="form-group">
          <label for="periode_uang_muka">Periode Uang Muka (Bulan)</label>
          <input type="number" id="periode_uang_muka" name="periode_uang_muka" value="4" min="1" max="36" oninput="generateAngsuranFields()">
        </div>
        
        <div class="form-group">
          <label for="uang_muka">Uang Muka</label>
          <div class="currency-input">
            <input type="number" id="uang_muka" name="uang_muka" value="0" readonly>
          </div>
        </div>
        
        <div class="form-group">
          <label for="total_akhir">Total Akhir</label>
          <div class="currency-input">
            <input type="number" id="total_akhir" name="total_akhir" value="0" readonly>
          </div>
        </div>
        
        <div class="form-group">
          <label for="cara_pembayaran">Cara Pembayaran</label>
          <input type="text" id="cara_pembayaran" name="cara_pembayaran">
        </div>
        
        <div class="form-group">
          <label for="periode_cicilan">Periode (Bulan)</label>
          <input type="number" id="periode_cicilan" name="periode_cicilan" oninput="updateCicilan()">
        </div>
        
        <div class="form-group">
          <label for="cicilan">Cicilan</label>
          <div class="currency-input">
            <input type="number" id="cicilan" name="cicilan" value="0" readonly>
          </div>
        </div>
      </div>
      
      <!-- Installment Fields -->
      <div class="form-group">
        <label>Angsuran Uang Muka</label>
        <div id="angsuran_container" class="angsuran-grid"></div>
      </div>
    </div>
    
    <!-- Section 5: Payment Schedule -->
    <div class="form-section">
      <h3>Jadwal Pembayaran</h3>
      <div class="form-grid">
        <div class="form-group">
          <label for="rencana_tgl_bayar_tanda_jadi">Rencana Tgl Bayar Tanda Jadi</label>
          <input type="date" id="rencana_tgl_bayar_tanda_jadi" name="rencana_tgl_bayar_tanda_jadi">
        </div>
        
        <div class="form-group">
          <label for="rencana_tgl_bayar_uang_muka">Rencana Tgl Bayar Uang Muka</label>
          <input type="date" id="rencana_tgl_bayar_uang_muka" name="rencana_tgl_bayar_uang_muka">
        </div>
        
        <div class="form-group">
          <label for="rencana_tgl_bayar_pembayaran">Rencana Tgl Bayar Pembayaran</label>
          <input type="date" id="rencana_tgl_bayar_pembayaran" name="rencana_tgl_bayar_pembayaran">
        </div>
      </div>
    </div>
    
    <button type="submit" class="btn btn-block">Simpan Transaksi</button>
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
    $(document).ready(function() {
    // Initialize Select2 for customer dropdown
    $('#konsumen').select2({
      placeholder: 'Ketik nama konsumen...',
      ajax: {
        url: 'cari_konsumen.php',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return { search: params.term };
        },
        processResults: function (data) {
          return {
            results: data.map(k => ({
              id: k.id,
              text: k.kode + ' - ' + k.nama_lengkap,
              kode: k.kode
            }))
          };
        },
        cache: true
      }
    });

    // Update customer code when customer is selected
    $('#konsumen').on('select2:select', function (e) {
      const selected = e.params.data;
      $('#kode_konsumen').val(selected.kode);
    });

    // Auto-fill property data when unit is selected
    $('#unit_select').on('change', function () {
      const selected = $(this).find(':selected');
      $('input[name="nama_unit"]').val(selected.data('nama'));
      $('#type').val(selected.data('type'));
      $('#luas_bangunan').val(selected.data('luas'));
      $('#harga').val(selected.data('harga'));
      $('#bonus').val(selected.data('bonus'));
      
      // Set harga_kesepakatan to harga if empty
      if ($('#harga_kesepakatan').val() == 0) {
        $('#harga_kesepakatan').val(selected.data('harga'));
        updatePenambahan();
      }
    });
    
    // Format currency inputs
    $('.currency-input input').on('focus', function() {
      $(this).parent().css('border-color', '#4361ee');
      $(this).parent().css('box-shadow', '0 0 0 3px rgba(67, 97, 238, 0.25)');
    }).on('blur', function() {
      $(this).parent().css('border-color', '#ced4da');
      $(this).parent().css('box-shadow', 'none');
    });
    
    // Initialize installment fields
    generateAngsuranFields();
  });

  function updatePenambahan() {
    const luas = parseFloat($("#luas_tanah").val()) || 0;
    const hargaPerM2 = parseFloat($("#harga_per_m2").val()) || 0;
    const lain = parseFloat($("#total_harga_penambahan_lain").val()) || 0;
    const kesepakatan = parseFloat($("#harga_kesepakatan").val()) || 0;

    const tanah = luas * hargaPerM2;
    $("#total_harga_penambahan").val(tanah.toFixed(2));

    const totalPenambahan = tanah + lain;
    $("#total_penambahan").val(totalPenambahan.toFixed(2));

    const totalSetelah = kesepakatan + totalPenambahan;
    $("#total_setelah_penambahan").val(totalSetelah.toFixed(2));

    updateUangMuka();
  }

  function generateAngsuranFields() {
    const container = $("#angsuran_container");
    container.empty();
    const periode = parseInt($("#periode_uang_muka").val()) || 0;
    
    for (let i = 1; i <= periode && i <= 36; i++) {
      const div = $('<div class="angsuran-item"></div>');
      div.append(`<label for="angsuran_${i}">Angsuran ${i}</label>`);
      div.append(`<div class="currency-input"><input type="number" id="angsuran_${i}" name="angsuran_${i}" value="0" oninput="updateUangMuka()"></div>`);
      container.append(div);
    }
    
    updateUangMuka();
  }

  function updateUangMuka() {
    const totalSetelah = parseFloat($("#total_setelah_penambahan").val()) || 0;
    let uangMuka = 0;

    $(".angsuran-item input").each(function() {
      uangMuka += parseFloat($(this).val()) || 0;
    });

    const tandaJadi = parseFloat($("#tanda_jadi").val()) || 0;
    const statusTandaJadi = $('input[name="status_tanda_jadi"]:checked').val();

    if (statusTandaJadi === "masuk") {
      uangMuka += tandaJadi;
    }

    $("#uang_muka").val(uangMuka.toFixed(2));
    $("#total_akhir").val((totalSetelah - uangMuka).toFixed(2));

    updateCicilan();
  }

  function updateCicilan() {
    const totalAkhir = parseFloat($("#total_akhir").val()) || 0;
    const periode = parseInt($("#periode_cicilan").val()) || 1;
    const cicilan = periode > 0 ? totalAkhir / periode : 0;
    $("#cicilan").val(cicilan.toFixed(2));
  }
</script>

</body>

</html>