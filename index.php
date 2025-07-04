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
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
<div id="controls-carousel" class="relative w-full max-w-screen-xl mx-auto mt-10 mb-8" data-carousel="static">
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
<section class="py-16 bg-white">
  <div class="max-w-6xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-center mb-4">Galeri Foto Unit</h2>
    <p class="text-gray-600 text-center mb-12 max-w-2xl mx-auto">
      Jelajahi berbagai pilihan unit kami melalui galeri foto berikut
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
      <!-- Ulangi blok ini untuk setiap kartu -->
      <div class="group relative overflow-hidden rounded-lg shadow-lg transition-transform transform hover:scale-105 cursor-pointer block">
        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80" alt="Unit A" class="w-full h-64 object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6">
          <h3 class="text-white text-xl font-bold mb-2">Unit A - Tipe 36</h3>
          <p class="text-gray-200 mb-4">Rumah minimalis dengan 2 kamar tidur dan 1 kamar mandi</p>
          <a href="/register?unit=Unit A - Tipe 36" class="self-start px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">Lihat Detail</a>
        </div>
      </div>

      <!-- Kartu Galeri lainnya tinggal copy bagian di atas dan ganti isinya -->
      <div class="group relative overflow-hidden rounded-lg shadow-lg transition-transform transform hover:scale-105 cursor-pointer block">
        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=800&q=80" alt="Unit B" class="w-full h-64 object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6">
          <h3 class="text-white text-xl font-bold mb-2">Unit B - Tipe 45</h3>
          <p class="text-gray-200 mb-4">Rumah modern dengan 3 kamar tidur dan 2 kamar mandi</p>
          <a href="/register?unit=Unit B - Tipe 45" class="self-start px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">Lihat Detail</a>
        </div>
      </div>

      <!-- Kartu Galeri 3 - Unit C -->
      <div class="group relative overflow-hidden rounded-lg shadow-lg transition-transform transform hover:scale-105 cursor-pointer block">
        <img src="https://images.unsplash.com/photo-1599427303058-f04cbcf4756f?auto=format&fit=crop&w=800&q=80" alt="Unit C" class="w-full h-64 object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6">
          <h3 class="text-white text-xl font-bold mb-2">Unit C - Tipe 50</h3>
          <p class="text-gray-200 mb-4">Rumah mewah dengan ruang keluarga luas</p>
          <a href="/register?unit=Unit C - Tipe 50" class="self-start px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">Lihat Detail</a>
        </div>
      </div>

      <!-- Kartu Galeri 4 - Unit D -->
      <div class="group relative overflow-hidden rounded-lg shadow-lg transition-transform transform hover:scale-105 cursor-pointer block">
        <img src="image/imah.jpg" alt="Unit D" class="w-full h-64 object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6">
          <h3 class="text-white text-xl font-bold mb-2">Unit D - Tipe 60</h3>
          <p class="text-gray-200 mb-4">Rumah dengan taman pribadi dan carport luas</p>
          <a href="/register?unit=Unit D - Tipe 60" class="self-start px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">Lihat Detail</a>
        </div>
      </div>

      <!-- Kartu Galeri 5 - Unit E -->
      <div class="group relative overflow-hidden rounded-lg shadow-lg transition-transform transform hover:scale-105 cursor-pointer block">
        <img src="image/imah2.jpg" alt="Unit E" class="w-full h-64 object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6">
          <h3 class="text-white text-xl font-bold mb-2">Unit E - Tipe 70</h3>
          <p class="text-gray-200 mb-4">Rumah dua lantai dengan ruang kerja pribadi</p>
          <a href="/register?unit=Unit E - Tipe 70" class="self-start px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">Lihat Detail</a>
        </div>
      </div>

      <!-- Contoh terakhir -->
      <div class="group relative overflow-hidden rounded-lg shadow-lg transition-transform transform hover:scale-105 cursor-pointer block">
        <img src="image/imah3.jpg" alt="Unit F" class="w-full h-64 object-cover">
        <div class="absolute inset-0 bg-black bg-opacity-50 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-6">
          <h3 class="text-white text-xl font-bold mb-2">Unit F - Tipe 90</h3>
          <p class="text-gray-200 mb-4">Rumah mewah dengan kolam renang pribadi</p>
          <a href="/register?unit=Unit F - Tipe 90" class="self-start px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 transition">Lihat Detail</a>
        </div>
      </div>
    </div>

    <div class="text-center mt-10">
      <a href="galeri.php" class="inline-block px-6 py-3 border-2 border-green-500 text-green-500 font-medium rounded-lg hover:bg-green-500 hover:text-white transition duration-300">
        Lihat Semua Foto
      </a>
    </div>
  </div>
</section>


<!-- Fasilitas Perumahan -->
<section class="py-16 bg-gray-50">
  <div class="max-w-6xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-center mb-4">Fasilitas Perumahan</h2>
    <p class="text-gray-600 text-center mb-12 max-w-2xl mx-auto">
      Nikmati berbagai fasilitas eksklusif yang tersedia untuk kenyamanan seluruh penghuni
    </p>
    
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
      <!-- Fasilitas 1 -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
        <img src="https://images.unsplash.com/photo-1574629810360-7efbbe195018?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Kolam Renang" class="w-full h-48 object-cover">
        <div class="p-6">
          <h3 class="text-xl font-bold mb-2">Kolam Renang</h3>
          <p class="text-gray-600">Kolam renang dengan area khusus anak-anak dan dewasa</p>
        </div>
      </div>
      
      <!-- Fasilitas 2 -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
        <img src="https://images.unsplash.com/photo-1571902943202-507ec2618e8f?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Taman Bermain" class="w-full h-48 object-cover">
        <div class="p-6">
          <h3 class="text-xl font-bold mb-2">Taman Bermain</h3>
          <p class="text-gray-600">Area bermain anak dengan peralatan modern dan aman</p>
        </div>
      </div>
      
      <!-- Fasilitas 3 -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
        <img src="https://images.unsplash.com/photo-1543351611-58f69d7c1781?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Jogging Track" class="w-full h-48 object-cover">
        <div class="p-6">
          <h3 class="text-xl font-bold mb-2">Jogging Track</h3>
          <p class="text-gray-600">Lari pagi atau sore di jalur khusus sepanjang 2km</p>
        </div>
      </div>
      
      <!-- Fasilitas 4 -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
        <img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Area Komersial" class="w-full h-48 object-cover">
        <div class="p-6">
          <h3 class="text-xl font-bold mb-2">Area Komersial</h3>
          <p class="text-gray-600">Pusat belanja dengan mini market, kafe, dan restoran</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Testimoni -->
<section class="py-16 bg-white">
  <div class="max-w-6xl mx-auto px-4">
    <h2 class="text-3xl font-bold text-center mb-4">Apa Kata Mereka?</h2>
    <p class="text-gray-600 text-center mb-12 max-w-2xl mx-auto">
      Testimoni dari penghuni yang sudah merasakan kenyamanan tinggal di perumahan kami
    </p>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <!-- Testimoni 1 -->
      <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
        <div class="flex items-center mb-4">
          <img src="https://randomuser.me/api/portraits/women/32.jpg" alt="Sarah Wijaya" class="w-12 h-12 rounded-full mr-4">
          <div>
            <h4 class="font-bold">Sarah Wijaya</h4>
            <p class="text-gray-500 text-sm">Penghuni sejak 2020</p>
          </div>
        </div>
        <p class="text-gray-700 mb-4">
          "Sangat puas dengan pelayanan dan fasilitas yang ada. Lingkungannya asri dan tetangganya ramah-ramah."
        </p>
        <div class="flex text-yellow-400" x-data="{ rating: 5 }">
          <template x-for="i in 5" :key="i">
            <svg
              class="w-5 h-5"
              fill="currentColor"
              viewBox="0 0 20 20"
              :class="i <= rating ? 'text-yellow-400' : 'text-gray-300'"
            >
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 
              3.292a1 1 0 00.95.69h3.462c.969 0 
              1.371 1.24.588 1.81l-2.8 2.034a1 1 0 
              00-.364 1.118l1.07 3.292c.3.921-.755 
              1.688-1.54 1.118l-2.8-2.034a1 1 0 
              00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 
              1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 
              1 0 00.951-.69l1.07-3.292z"/>
            </svg>
          </template>
        </div>
      </div>

      <!-- Testimoni 2 -->
      <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
        <div class="flex items-center mb-4">
          <img src="https://randomuser.me/api/portraits/men/44.jpg" alt="Andi Prasetyo" class="w-12 h-12 rounded-full mr-4">
          <div>
            <h4 class="font-bold">Andi Prasetyo</h4>
            <p class="text-gray-500 text-sm">Penghuni sejak 2019</p>
          </div>
        </div>
        <p class="text-gray-700 mb-4">
          "Lokasinya strategis, dekat dengan pusat kota dan sekolah. Anak-anak betah tinggal di sini."
        </p>
        <div class="flex text-yellow-400" x-data="{ rating: 5 }">
          <template x-for="i in 5" :key="i">
            <svg
              class="w-5 h-5"
              fill="currentColor"
              viewBox="0 0 20 20"
              :class="i <= rating ? 'text-yellow-400' : 'text-gray-300'"
            >
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 
              3.292a1 1 0 00.95.69h3.462c.969 0 
              1.371 1.24.588 1.81l-2.8 2.034a1 1 0 
              00-.364 1.118l1.07 3.292c.3.921-.755 
              1.688-1.54 1.118l-2.8-2.034a1 1 0 
              00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 
              1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 
              1 0 00.951-.69l1.07-3.292z"/>
            </svg>
          </template>
        </div>
      </div>

      <!-- Testimoni 3 -->
      <div class="bg-gray-50 p-6 rounded-lg shadow-sm">
        <div class="flex items-center mb-4">
          <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Rina Marlina" class="w-12 h-12 rounded-full mr-4">
          <div>
            <h4 class="font-bold">Rina Marlina</h4>
            <p class="text-gray-500 text-sm">Penghuni sejak 2021</p>
          </div>
        </div>
        <p class="text-gray-700 mb-4">
          "Rumahnya nyaman dan pengelolaan lingkungannya sangat teratur. Ada banyak taman untuk anak-anak bermain."
        </p>
        <div class="flex text-yellow-400" x-data="{ rating: 5 }">
          <template x-for="i in 5" :key="i">
            <svg
              class="w-5 h-5"
              fill="currentColor"
              viewBox="0 0 20 20"
              :class="i <= rating ? 'text-yellow-400' : 'text-gray-300'"
            >
              <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 
              3.292a1 1 0 00.95.69h3.462c.969 0 
              1.371 1.24.588 1.81l-2.8 2.034a1 1 0 
              00-.364 1.118l1.07 3.292c.3.921-.755 
              1.688-1.54 1.118l-2.8-2.034a1 1 0 
              00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 
              1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 
              1 0 00.951-.69l1.07-3.292z"/>
            </svg>
          </template>
        </div>
      </div>

    </div>
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
