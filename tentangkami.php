<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Unit Perumahan - Tentang Kami</title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js" defer></script>
</head>
<body class="bg-gray-100 min-h-screen flex font-sans">

  <!-- Sidebar Toggle Button -->
  <button id="sidebarToggle" class="text-gray-700 bg-white p-2 rounded-md border border-gray-300 fixed top-4 left-4 z-50 shadow-md hover:bg-gray-50">
    &#9776;
  </button>

  <!-- Sidebar -->
  <?php include 'templates/sidebar.php'; ?>

  <!-- Main Content -->
  <div id="main-content" class="flex-1 ml-64 p-6 transition-all duration-300 ease-out">

    <!-- Navbar -->
    <div id="navbar" class="rounded-md fixed top-0 left-0 w-full z-10 transition-all duration-300 ease-out ml-64">
      <?php include 'templates/navbar.php'; ?>
    </div>

    <!-- Header -->
    <header class="bg-white shadow-md mt-16 rounded-lg p-6">
      <h1 class="text-3xl font-extrabold text-gray-800">Tentang Kami</h1>
      <p class="text-gray-600 mt-2">Mengenal lebih dekat visi, misi, dan tim hebat kami.</p>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto py-8">
      <!-- Tentang Perusahaan -->
      <section class="bg-white shadow-md rounded-lg p-8 mb-12">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Tentang Perusahaan Kami</h2>
        <p class="text-gray-700 leading-relaxed mb-4">
          Kami adalah perusahaan yang bergerak di bidang pengembangan perumahan. Dengan pengalaman bertahun-tahun, kami berkomitmen untuk menyediakan hunian yang nyaman, aman, dan terjangkau bagi masyarakat.
        </p>
        <p class="text-gray-700 leading-relaxed">
          Visi kami adalah menjadi pengembang perumahan terkemuka yang memberikan solusi hunian terbaik bagi keluarga Indonesia. Misi kami adalah menghadirkan perumahan berkualitas dengan desain modern dan fasilitas lengkap.
        </p>
      </section>

      <!-- Tim Kami -->
      <section>
        <h3 class="text-2xl font-bold text-gray-800 mb-6 text-center">Tim Kami</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
          
          <!-- Card 1 -->
          <div class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl transition duration-300">
            <img src="path/to/image1.jpg" alt="CEO" class="w-24 h-24 mx-auto rounded-full mb-4 object-cover border-4 border-gray-200" 
              onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=John+Doe&background=random';">
            <h4 class="text-lg font-semibold text-gray-800">John Doe</h4>
            <p class="text-gray-500">CEO</p>
          </div>

          <!-- Card 2 -->
          <div class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl transition duration-300">
            <img src="path/to/image2.jpg" alt="CTO" class="w-24 h-24 mx-auto rounded-full mb-4 object-cover border-4 border-gray-200" 
              onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=Jane+Smith&background=random';">
            <h4 class="text-lg font-semibold text-gray-800">Jane Smith</h4>
            <p class="text-gray-500">CTO</p>
          </div>

          <!-- Card 3 -->
          <div class="bg-white shadow-lg rounded-xl p-6 text-center hover:shadow-2xl transition duration-300">
            <img src="path/to/image3.jpg" alt="COO" class="w-24 h-24 mx-auto rounded-full mb-4 object-cover border-4 border-gray-200" 
              onerror="this.onerror=null;this.src='https://ui-avatars.com/api/?name=Michael+Brown&background=random';">
            <h4 class="text-lg font-semibold text-gray-800">Michael Brown</h4>
            <p class="text-gray-500">COO</p>
          </div>

        </div>
      </section>
    </main>
  </div>

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
      height: 100vh;
      overflow-y: auto;
      overflow-x: hidden;
    }
    .-translate-x-full { transform: translateX(-100%); }
    .ml-0 { margin-left: 0 !important; }
    .ml-64 { margin-left: 16rem !important; }
    .pl-16 { padding-left: 4rem !important; }
    #main-content, #navbar {
      transition: margin-left 0.3s ease-out, padding-left 0.3s ease-out;
    }
  </style>
</body>
</html>
