<?php
session_start();
include 'routes/config.php';  // Pastikan koneksi MySQLi dimuat

// Cek apakah user sudah login
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit;
}

// Ambil data pengguna berdasarkan session
$username = $_SESSION['username'];
$query = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($query); // Menggunakan $conn untuk MySQLi
$stmt->bind_param('s', $username); // Binding parameter untuk menghindari SQL Injection
$stmt->execute();
$result = $stmt->get_result(); // Menjalankan query
$user = $result->fetch_assoc(); // Mengambil data pengguna

// Cek jika $user kosong
if ($user === null) {
    // Jika data user tidak ditemukan, arahkan ke halaman login
    $_SESSION['error_message'] = 'Pengguna tidak ditemukan.';
    header('Location: login.php');
    exit;
}

// Proses perubahan data identitas dan upload foto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $new_username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Proses upload foto
    $photo = $user['photo'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo = $target_file;
        }
    }

    // Validasi password jika ada perubahan
    if (!empty($password)) {
        if ($password !== $confirm_password) {
            $_SESSION['error_message'] = "Password dan konfirmasi password tidak cocok.";
            header('Location: profil.php');
            exit;
        }
    }

    // Update data pengguna (termasuk username dan password)
    if (!empty($password)) {
        $updateQuery = "UPDATE users SET name = ?, email = ?, username = ?, password = ?, photo = ? WHERE username = ?";
        $updateStmt = $conn->prepare($updateQuery); // Menggunakan $conn untuk MySQLi
        $updateStmt->bind_param('ssssss', $name, $email, $new_username, $password, $photo, $username); // Binding parameter
    } else {
        $updateQuery = "UPDATE users SET name = ?, email = ?, username = ?, photo = ? WHERE username = ?";
        $updateStmt = $conn->prepare($updateQuery); // Menggunakan $conn untuk MySQLi
        $updateStmt->bind_param('sssss', $name, $email, $new_username, $photo, $username); // Binding parameter
    }

    $updateStmt->execute();

    // Jika username diubah, update session username
    if ($new_username !== $username) {
        $_SESSION['username'] = $new_username;  // Mengupdate session dengan username baru
    }

    // Set pesan sukses
    $_SESSION['success_message'] = 'Profil berhasil diperbarui!';
    header('Location: profil.php');
    exit;
}
?>




<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Unit Perumahan - Profil</title>
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

    <!-- Profil Section -->
    <div class="flex justify-center pt-8 mt-16">
      <div class="max-w-7xl w-full bg-white p-6 rounded-lg shadow-lg">
        <h2 class="text-2xl font-semibold mb-6">Profil Saya</h2>

          <!-- Menampilkan pesan sukses -->
          <?php if (isset($_SESSION['success_message'])): ?>
            <div id="success-message" class="bg-green-500 text-white p-4 rounded-lg mb-6">
              <?php echo $_SESSION['success_message']; ?>
            </div>
            <?php unset($_SESSION['success_message']); ?> <!-- Hapus pesan setelah ditampilkan -->
          <?php endif; ?>

        <!-- Form Edit Profil -->
        <form action="profil.php" method="POST" enctype="multipart/form-data" class="space-y-6">
          <div>
            <label for="name" class="block mb-2 text-sm font-medium text-gray-900">Nama</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
          </div>

          <div>
            <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
          </div>

          <div>
            <label for="username" class="block mb-2 text-sm font-medium text-gray-900">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username'] ?? ''); ?>" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" required>
          </div>

          <div>
            <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password Baru</label>
            <input type="password" id="password" name="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
          </div>

          <div>
            <label for="confirm_password" class="block mb-2 text-sm font-medium text-gray-900">Konfirmasi Password Baru</label>
            <input type="password" id="confirm_password" name="confirm_password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
          </div>

          <div>
            <label for="photo" class="block mb-2 text-sm font-medium text-gray-900">Foto Profil</label>
            <input type="file" id="photo" name="photo" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
            <?php if (!empty($user['photo'])): ?>
              <img src="<?php echo htmlspecialchars($user['photo'] ?? ''); ?>" alt="Foto Profil" class="mt-4 w-32 h-32 rounded-full object-cover border-2 border-gray-300">
            <?php endif; ?>
          </div>

          <div class="flex justify-end">
            <button type="submit" class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-6 py-2 text-center">
                Simpan Perubahan
            </button>
          </div>
        </form>
      </div>
    </div>


<script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>
<script>
  // Menambahkan delay 5 detik untuk menghilangkan pesan
  setTimeout(function() {
    var message = document.getElementById('success-message');
    if (message) {
      message.style.display = 'none'; // Menyembunyikan pesan setelah 5 detik
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
</html>
