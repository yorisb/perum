<?php
$defaultAvatar = 'data:image/svg+xml;base64,' . base64_encode(
  '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
  </svg>
  '
);
?>

<!-- Sidebar -->
<aside id="sidebar" class="w-64 bg-white shadow-md h-screen fixed z-40 flex flex-col transform transition-transform duration-300 ease-in-out">
  <div class="p-4 border-b mt-16">
    <h1 class="text-xl font-bold">Unit Perumahan</h1>
  </div>
  <nav class="mt-4 flex-1">
    <ul class="space-y-2 px-4">

    <!-- Combo Button untuk Dashboard -->
    <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' || basename($_SERVER['PHP_SELF']) == 'tentangkami.php' ? 'bg-green-100 dark:bg-green-700' : ''; ?>" aria-controls="dashboardDropdown" data-collapse-toggle="dashboardDropdown">
      <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Dashboard</span>
      <svg class="w-3 h-3 ml-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <ul id="dashboardDropdown" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' || basename($_SERVER['PHP_SELF']) == 'tentangkami.php' ? '' : 'hidden'; ?> py-2 space-y-2">

      <!-- Link ke Dashboard -->
      <li>
        <a href="dashboard.php" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-green-100 dark:bg-green-700' : ''; ?>">
          <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10l1.664-2.496A2 2 0 016.334 6h11.332a2 2 0 011.67.504L21 10m-9 4v6m-4-6v6m8-6v6M5 10h14a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2-2 0 012-2z"></path>
          </svg>
          <span class="ml-3">Dashboard</span>
        </a>
      </li>

      <!-- Link ke Tentang Kami -->
      <li>
        <a href="tentangkami.php" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'tentangkami.php' ? 'bg-green-100 dark:bg-green-700' : ''; ?>">
          <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16h6m2 4H7a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v12a2 2 0 01-2 2z"></path>
          </svg>
          <span class="ml-3">Tentang Kami</span>
        </a>
      </li>

    </ul>


    <!-- Combo Button untuk Transaksi -->
    <?php if ($_SESSION['role'] == 'superadmin' || $_SESSION['role'] == 'admin'): ?>
    <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo in_array(basename($_SERVER['PHP_SELF']), ['daftar_followup.php', 'daftar_konsumen.php', 'daftar_transaksi.php', 'form_jejak_followup.php', 'input_calon_konsumen.php', 'detail_konsumen.php', 'edit_konsumen.php', 'transaksi.php', 'detail_transaksi.php', 'cetak_transaksi.php','pembayaran.php', 'history_pembayaran.php']) ? 'bg-green-100 dark:bg-green-700' : ''; ?>" aria-controls="transaksiDropdown" data-collapse-toggle="transaksiDropdown">
      <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Transaksi</span>
      <svg class="w-3 h-3 ml-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
      </svg>
    </button>
    <!-- Dropdown Menu -->
    <ul id="transaksiDropdown" class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['daftar_followup.php', 'daftar_konsumen.php', 'daftar_transaksi.php', 'form_jejak_followup.php', 'input_calon_konsumen.php', 'detail_konsumen.php', 'edit_konsumen.php', 'transaksi.php', 'detail_transaksi.php', 'cetak_transaksi.php', 'pembayaran.php', 'history_pembayaran.php']) ? '' : 'hidden'; ?> py-2 space-y-2">
    <li>
        <a href="daftar_konsumen.php" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo in_array(basename($_SERVER['PHP_SELF']), ['daftar_konsumen.php', 'input_calon_konsumen.php', 'detail_konsumen.php', 'edit_konsumen.php']) ? 'bg-green-100 dark:bg-green-700' : ''; ?>">
          <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white " fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M9 20H4v-2a3 3 0 015.356-1.857M15 11a4 4 0 10-8 0 4 4 0 008 0z" />
          </svg>
          <span class="ml-3">Daftar Konsumen</span>
        </a>
      </li>  
      <li>
        <a href="daftar_followup.php" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo in_array(basename($_SERVER['PHP_SELF']), ['daftar_followup.php', 'form_jejak_followup.php']) ? 'bg-green-100 dark:bg-green-700' : ''; ?>">
          <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white " fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <span class="ml-3">Daftar Follow Up</span>
        </a>
      </li>
      <li>
        <a href="daftar_transaksi.php" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo in_array (basename($_SERVER['PHP_SELF']), [ 'daftar_transaksi.php', 'transaksi.php', 'detail_transaksi.php', 'cetak_transaksi.php']) ? 'bg-green-100 dark:bg-green-700' : ''; ?>">
          <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white " fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v2m14 0h1a2 2 0 012 2v7a2 2 0 01-2 2h-1m-14 0a2 2 0 01-2-2v-7a2-2 0 012-2h14m-10 4h4" />
          </svg>
          <span class="ml-3">Daftar Transaksi</span>
        </a>
      </li>
      <li>
        <a href="pembayaran.php" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'pembayaran.php' ? 'bg-green-100 dark:bg-green-700' : ''; ?>">
          <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
          <span class="ml-3">Tempo Pembayaran</span>
        </a>
      </li>
      <li>
        <a href="history_pembayaran.php" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'history_pembayaran.php' ? 'bg-green-100 dark:bg-green-700' : ''; ?>">
          <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
          <span class="ml-3">History Pembayaran</span>
        </a>
      </li>
    </ul>
    <?php endif; ?>



      <!-- Combo Button untuk laporan -->
      <?php if ($_SESSION['role'] == 'superadmin' || $_SESSION['role'] == 'admin'): ?>
        <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700" aria-controls="laporanDropdown" data-collapse-toggle="laporanDropdown">
          <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Laporan</span>
          <svg class="w-3 h-3 ml-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
          </svg>
        </button>
        <ul id="laporanDropdown" class="hidden py-2 space-y-2">
          <li>
            <a href="#" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
              <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
              <span class="ml-3">Lap Pemasukan</span>
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
              <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
              </svg>
              <span class="ml-3">Lap Pengeluaran</span>
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
              <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <span class="ml-3">Rekap Piutang</span>
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
              <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
              </svg>
              <span class="ml-3">Legalitas</span>
            </a>
          </li>
          <li>
            <a href="#" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
              <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
              </svg>
              <span class="ml-3">Lap Pembatalan Transaksi</span>
            </a>
          </li>
        </ul>
      <?php endif; ?>
      
      <!-- Combo Button untuk Keuangan -->
      <?php if ($_SESSION['role'] == 'superadmin' || $_SESSION['role'] == 'admin'): ?>
        <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo in_array(basename($_SERVER['PHP_SELF']), ['tampil_pemasukan.php', 'form_pemasukan.php', 'form_pengeluaran.php']) ? 'bg-green-100 dark:bg-green-700' : ''; ?>" aria-controls="keuanganDropdown" data-collapse-toggle="keuanganDropdown">
          <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Keuangan</span>
          <svg class="w-3 h-3 ml-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
          </svg>
        </button>

        <!-- Dropdown Menu Keuangan -->
        <ul id="keuanganDropdown" class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['tampil_pemasukan.php', 'form_pemasukan.php', 'form_pengeluaran.php']) ? '' : 'hidden'; ?> py-2 space-y-2">
          <li>
            <a href="tampil_pemasukan.php" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo in_array(basename($_SERVER['PHP_SELF']), ['tampil_pemasukan.php', 'form_pemasukan.php']) ? 'bg-green-100 dark:bg-green-700' : ''; ?>">
              <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
              </svg>
              <span class="ml-3">Pemasukan</span>
            </a>
          </li>
          <li>
            <a href="form_pengeluaran.php" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'form_pengeluaran.php' ? 'bg-green-100 dark:bg-green-700' : ''; ?>">
              <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path>
              </svg>
              <span class="ml-3">Pengeluaran</span>
            </a>
          </li>
        </ul>
      <?php endif; ?>


      <!-- Combo Button untuk Karyawan -->
    <?php if ($_SESSION['role'] == 'superadmin' || $_SESSION['role'] == 'admin'): ?>
      <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo in_array(basename($_SERVER['PHP_SELF']), ['karyawan.php', 'add_karyawan.php', 'karyawan_resign.php', 'edit_karyawan.php']) ? 'bg-green-100 dark:bg-green-700' : ''; ?>" aria-controls="karyawanDropdown" data-collapse-toggle="karyawanDropdown">
        <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Karyawan</span>
        <svg class="w-3 h-3 ml-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
        </svg>
      </button>

      <!-- Dropdown Menu -->
      <ul id="karyawanDropdown" class="<?php echo in_array(basename($_SERVER['PHP_SELF']), ['karyawan.php', 'add_karyawan.php', 'karyawan_resign.php', 'edit_karyawan.php']) ? '' : 'hidden'; ?> py-2 space-y-2">

        <li>
          <a href="karyawan.php" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'karyawan.php' ? 'bg-green-100 dark:bg-green-700' : ''; ?>">
          <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v1h8v-1zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-1a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v1h-3zM4.75 12.094A5.973 5.973 0 004 15v1H1v-1a3 3 0 013.75-2.906z"></path>
            </svg>
            <span class="ml-3">Karyawan</span>
          </a>
        </li>
        
        <!-- <li>
          <a href="karyawan_resign.php" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo basename($_SERVER['PHP_SELF']) == 'karyawan_resign.php' ? 'bg-green-100 dark:bg-green-700' : ''; ?>">
          <svg class="w-5 h-5 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:text-gray-400 dark:group-hover:text-white" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" d="M11 4a4 4 0 100 8 4 4 0 000-8zM6.75 8a.75.75 0 000 1.5h8.5a.75.75 0 000-1.5h-8.5z" clip-rule="evenodd"></path>
              <path d="M4.75 14a.75.75 0 00-.75.75v1.5c0 .414.336.75.75.75h10.5a.75.75 0 00.75-.75v-1.5a.75.75 0 00-.75-.75H4.75z"></path>
            </svg>
            <span class="ml-3">Karyawan Resign</span>
          </a>
        </li> -->

      </ul>
    <?php endif; ?>

      <!-- Combo Button untuk Setting Unit -->
      <?php if ($_SESSION['role'] == 'superadmin' || $_SESSION['role'] == 'admin'): ?>
        <?php
          // Cek apakah halaman aktif berada di dalam folder unit/ dan file unit.php
          $currentPage = basename($_SERVER['PHP_SELF']);
          $isUnitActive = strpos($_SERVER['PHP_SELF'], 'unit.php') !== false;
        ?>
        <button type="button" class="flex items-center w-full p-2 text-base text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo $isUnitActive ? 'bg-green-100 dark:bg-green-700' : ''; ?>" aria-controls="setproyDropdown" data-collapse-toggle="setproyDropdown">
          <span class="flex-1 ms-3 text-left rtl:text-right whitespace-nowrap">Setting Unit</span>
          <svg class="w-3 h-3 ml-auto" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
          </svg>
        </button>

        <!-- Dropdown Menu -->
        <ul id="setproyDropdown" class="<?php echo $isUnitActive ? '' : 'hidden'; ?> py-2 space-y-2">
          <li>
            <a href="unit.php" class="flex items-center w-full p-2 text-base transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700 <?php echo $isUnitActive ? 'bg-green-100 dark:bg-green-700' : ''; ?>">
              <svg class="w-6 h-6 text-gray-500 transition duration-75 group-hover:text-gray-900 dark:group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
              </svg>
              <span class="ml-3">Unit Properti</span>
            </a>
          </li>
        </ul>
      <?php endif; ?>

        <!-- Profile/Login -->
        <div class="relative border-t pt-4 pb-32">
          <?php if (isset($_SESSION['username'])): ?>
            <button id="sidebar-profile-button" class="flex items-center space-x-2 focus:outline-none w-full">
              <img 
                class="w-8 h-8 rounded-full object-cover" 
                src="<?= !empty($user['photo']) ? htmlspecialchars($user['photo']) : $defaultAvatar; ?>" 
                alt="user photo"
              />
              <span class="text-gray-700 font-medium"><?= htmlspecialchars($_SESSION['username']); ?></span>
              <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Ganti dropdown div ke ul/li -->
            <ul id="sidebar-profile-dropdown" class="hidden absolute right-4 mt-2 w-48 bg-white rounded-md shadow-lg z-50">
              <li>
                <a href="profil.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profil Saya</a>
              </li>
              <li>
                <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">Logout</a>
              </li>
            </ul>
          <?php endif; ?>

        </div>
    </ul>
  </nav>

  <!-- Footer Sidebar -->
  <div class="p-4 border-t text-center text-sm text-gray-500">
    <p>&copy; 2025 GenIT. All rights reserved.</p>
  </div>
</aside>

<script>
  // Get all combo buttons
  const comboButtons = document.querySelectorAll('[data-collapse-toggle]');
  comboButtons.forEach(button => {
    const targetId = button.getAttribute('aria-controls');
    const target = document.getElementById(targetId);

    button.addEventListener('click', () => {
      target.classList.toggle('hidden');
    });
  });
</script>

<script>

  // ==== DROPDOWN BUTTONS ====
  const dropdownButtons = [
    { button: '[aria-controls="dashboardDropdown"]', dropdownId: 'dashboardDropdown' },
    { button: '[aria-controls="laporanDropdown"]', dropdownId: 'laporanDropdown' },
    { button: '[aria-controls="pembayaranDropdown"]', dropdownId: 'pembayaranDropdown' },
    { button: '[aria-controls="operasionalDropdown"]', dropdownId: 'operasionalDropdown' },
    { button: '[aria-controls="setproyDropdown"]', dropdownId: 'setproyDropdown' },
    { button: '[aria-controls="keuanganDropdown"]', dropdownId: 'keuanganDropdown' }, // Tambahkan ini
  ];

  dropdownButtons.forEach(({ button, dropdownId }) => {
    const btn = document.querySelector(button);
    const dropdown = document.getElementById(dropdownId);

    if (btn && dropdown) {
      btn.addEventListener('click', () => {
        dropdown.classList.toggle('hidden');
      });
    }
  });

  // ==== SIDEBAR PROFILE DROPDOWN ====
  const sidebarProfileButton = document.getElementById('sidebar-profile-button');
  const sidebarProfileDropdown = document.getElementById('sidebar-profile-dropdown');

  if (sidebarProfileButton && sidebarProfileDropdown) {
    sidebarProfileButton.addEventListener('click', (e) => {
      e.stopPropagation(); // Hindari tertutup saat diklik
      sidebarProfileDropdown.classList.toggle('hidden');
    });

    // Tutup dropdown saat klik di luar
    window.addEventListener('click', (e) => {
      if (
        !sidebarProfileDropdown.classList.contains('hidden') &&
        !sidebarProfileButton.contains(e.target) &&
        !sidebarProfileDropdown.contains(e.target)
      ) {
        sidebarProfileDropdown.classList.add('hidden');
      }
    });
  }
</script>
