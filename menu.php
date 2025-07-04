<?php
session_start(); // Mulai session di setiap halaman

// Include koneksi database
include 'routes/config.php'; // Pastikan file koneksi database Anda sudah benar

// Cek apakah user sudah login
if (isset($_SESSION['username'])) {
    // Ambil data pengguna berdasarkan session
    $username = $_SESSION['username'];

    // Pastikan Anda sudah menyiapkan koneksi ke database
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Misalnya foto profil
        $photo = $user['photo'];
    } else {
        $_SESSION['error_message'] = "Pengguna tidak ditemukan!";
        header('Location: login.php');
        exit;
    }
} else {
    // Jika belum login, arahkan ke halaman login atau tampilkan tombol login
    $photo = null; // Tidak ada foto profil jika belum login
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Unit Perumahan - Carousel</title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js" defer></script>
</head>
<body class="bg-gray-100 min-h-screen">

<!-- Navbar -->
<?php include 'templates/navbar.php'; ?>


    <!-- Hero / Deskripsi Singkat -->
    <section class="max-w-4xl mx-auto mt-10 px-4 text-center animate-on-scroll">
        <h2 class="text-3xl font-bold mb-4">Temukan Hunian Impian Anda</h2>
        <p class="text-gray-600 mb-6">
        Kami menawarkan berbagai pilihan unit perumahan berkualitas, nyaman, dan strategis untuk keluarga Anda. 
        Lihat detail setiap unit di bawah ini dan temukan rumah yang paling cocok untuk kebutuhan Anda.
        </p>
    </section>


<!-- Carousel -->
<div id="controls-carousel" class="relative w-full max-w-4xl mx-auto mt-10" data-carousel="static">
  <!-- Carousel wrapper -->
  <div class="relative h-96 overflow-hidden rounded-lg">
    
    <!-- Slide 1 -->
    <div class="hidden duration-700 ease-in-out" data-carousel-item>
      <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80" 
           class="absolute block w-full h-full object-cover" alt="Unit A" />
      <div class="absolute bottom-0 bg-black bg-opacity-50 w-full p-4 text-white">
        <h2 class="text-xl font-bold">Unit A - Tipe 36</h2>
        <p>2 Kamar Tidur • 1 Kamar Mandi • LT 72m²</p>
        <p class="mt-1 font-semibold">Rp250.000.000</p>
        <a href="detail-unit-a.html" class="inline-block mt-2 px-4 py-2 bg-green-500 hover:bg-green-700 text-white text-sm rounded-md transition">Selengkapnya</a>
      </div>
    </div>

    <!-- Slide 2 -->
    <div class="hidden duration-700 ease-in-out" data-carousel-item>
      <img src="image/imah.jpg" 
           class="absolute block w-full h-full object-cover" alt="Unit B" />
      <div class="absolute bottom-0 bg-black bg-opacity-50 w-full p-4 text-white">
        <h2 class="text-xl font-bold">Unit B - Tipe 45</h2>
        <p>3 Kamar Tidur • 2 Kamar Mandi • LT 90m²</p>
        <p class="mt-1 font-semibold">Rp350.000.000</p>
        <a href="detail-unit-b.html" class="inline-block mt-2 px-4 py-2 bg-green-500 hover:bg-green-700 text-white text-sm rounded-md transition">Selengkapnya</a>
      </div>
    </div>

    <!-- Slide 3 -->
    <div class="hidden duration-700 ease-in-out" data-carousel-item>
      <img src="https://images.unsplash.com/photo-1599427303058-f04cbcf4756f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80" 
           class="absolute block w-full h-full object-cover" alt="Unit C" />
      <div class="absolute bottom-0 bg-black bg-opacity-50 w-full p-4 text-white">
        <h2 class="text-xl font-bold">Unit C - Tipe 60</h2>
        <p>3+1 Kamar Tidur • 2 Kamar Mandi • LT 120m²</p>
        <p class="mt-1 font-semibold">Rp500.000.000</p>
        <a href="detail-unit-c.html" class="inline-block mt-2 px-4 py-2 bg-green-500 hover:bg-green-700 text-white text-sm rounded-md transition">Selengkapnya</a>
      </div>
    </div>

  </div>

  <!-- Controls -->
  <button type="button" class="absolute top-0 left-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
    <span class="inline-flex items-center justify-center w-10 h-10 bg-white/70 rounded-full group-hover:bg-white/90">
      <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
      </svg>
    </span>
  </button>
  <button type="button" class="absolute top-0 right-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
    <span class="inline-flex items-center justify-center w-10 h-10 bg-white/70 rounded-full group-hover:bg-white/90">
      <svg class="w-5 h-5 text-gray-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
      </svg>
    </span>
  </button>
</div>

<!-- Galeri Foto Card -->
<section class="max-w-7xl mx-auto px-4 mt-16">
  <h2 class="text-2xl font-bold mb-6 text-center text-gray-800">Galeri Foto Unit</h2>
  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
    
    <!-- Kartu Galeri -->
    <a href="/register?unit=Unit A - Tipe 36" class="card relative overflow-hidden rounded-lg shadow-lg transition-transform transform hover:scale-105 cursor-pointer block">
      <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80" alt="Unit A" class="w-full h-64 object-cover">
      <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
        <p class="text-white text-lg font-semibold">Unit A - Tipe 36</p>
      </div>
    </a>

    <a href="/register?unit=Unit B - Tipe 45" class="card relative overflow-hidden rounded-lg shadow-lg transition-transform transform hover:scale-105 cursor-pointer block">
      <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80" alt="Unit B" class="w-full h-64 object-cover">
      <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
        <p class="text-white text-lg font-semibold">Unit B - Tipe 45</p>
      </div>
    </a>

    <a href="/register?unit=Unit C - Tipe 50" class="card relative overflow-hidden rounded-lg shadow-lg transition-transform transform hover:scale-105 cursor-pointer block">
      <img src="https://images.unsplash.com/photo-1599427303058-f04cbcf4756f?auto=format&fit=crop&w=800&q=80" alt="Unit C" class="w-full h-64 object-cover">
      <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
        <p class="text-white text-lg font-semibold">Unit C - Tipe 50</p>
      </div>
    </a>

    <a href="/register?unit=Unit D - Tipe 60" class="card relative overflow-hidden rounded-lg shadow-lg transition-transform transform hover:scale-105 cursor-pointer block">
      <img src="image/imah.jpg" alt="Unit D" class="w-full h-64 object-cover">
      <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
        <p class="text-white text-lg font-semibold">Unit D - Tipe 60</p>
      </div>
    </a>

    <a href="/register?unit=Unit E - Tipe 70" class="card relative overflow-hidden rounded-lg shadow-lg transition-transform transform hover:scale-105 cursor-pointer block">
      <img src="image/imah2.jpg" alt="Unit E" class="w-full h-64 object-cover">
      <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
        <p class="text-white text-lg font-semibold">Unit E - Tipe 70</p>
      </div>
    </a>

    <a href="/register?unit=Unit F - Tipe 90" class="card relative overflow-hidden rounded-lg shadow-lg transition-transform transform hover:scale-105 cursor-pointer block">
      <img src="image/imah3.jpg" alt="Unit F" class="w-full h-64 object-cover">
      <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
        <p class="text-white text-lg font-semibold">Unit F - Tipe 90</p>
      </div>
    </a>
  </div>
</section>


<!-- Video Properti -->
<section class="max-w-4xl mx-auto mt-16 px-4 text-center">
  <h2 class="text-2xl font-bold mb-4 text-gray-800">Video Tour Properti</h2>
  <p class="text-gray-600 mb-6">Lihat secara langsung suasana dalam rumah melalui video berikut:</p>
  <div class="aspect-w-16 aspect-h-9 rounded-lg shadow-md overflow-hidden">
    <iframe class="w-full h-96" src="https://www.youtube.com/embed/NT0uNbfhX6U" title="Video Tour Rumah" allowfullscreen></iframe>
  </div>
</section>

<!-- Animasi CTA -->
<section class="mt-20 text-center">
  <div class="bg-green-100 py-10 px-6 rounded-xl shadow-md max-w-3xl mx-auto animate-bounce">
    <h3 class="text-2xl font-bold text-green-700">Siap Memiliki Hunian Impianmu?</h3>
    <p class="mt-2 text-gray-600">Hubungi kami sekarang untuk konsultasi dan kunjungan lokasi gratis!</p>
    <a href="kontak.php" class="mt-4 inline-block bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-300">Hubungi Kami</a>
  </div>
</section>

  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>
</body>
<?php include 'templates/footer.php'; ?>
</html>
