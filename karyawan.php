<?php
session_start();

// Check if user is logged in, redirect to login if not
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

include 'routes/config.php';
$username = $_SESSION['username'];
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get and clear success message from session
$successMessage = '';
if (!empty($_SESSION['success_message'])) {
    $successMessage = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

$defaultAvatar = 'data:image/svg+xml;base64,' . base64_encode(
    '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
      <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
    </svg>
    '
  );
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Unit Perumahan - Data Karyawan</title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    <div id="navbar" class="rounded-md fixed top-0 left-0 w-full z-10 transition-all duration-300 ease-out ml-64">
      <?php include 'templates/navbar.php'; ?>
    </div>

    <!-- Content -->
    <div class="mt-20">
      <!-- Success Message -->
      <?php if (!empty($successMessage)): ?>
        <div id="success-alert" class="fixed top-6 left-1/2 transform -translate-x-1/2 w-80 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-md shadow-md flex items-start gap-3 text-sm animate-fade-in-down z-50 transition-opacity duration-500">
          <svg class="w-5 h-5 mt-0.5 text-green-700 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          <div class="flex-1">
            <p class="font-semibold">Sukses!</p>
            <p class="text-xs"><?= htmlspecialchars($successMessage); ?></p>
          </div>
        </div>

        <script>
          setTimeout(() => {
            const alert = document.getElementById('success-alert');
            if (alert) {
              alert.classList.add('opacity-0');
              setTimeout(() => alert.remove(), 500);
            }
          }, 5000);
        </script>
      <?php endif; ?>

      <!-- Page Header -->
      <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Data Karyawan</h1>
        <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md flex items-center">
        <a href="add_karyawan.php"><i class="fas fa-plus mr-2"></i> Tambah Karyawan</a>
        </button>
      </div>

      <!-- Employee Table -->
      <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jabatan</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departemen</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <!-- Sample Data - Replace with PHP loop -->
              <?php
                // Koneksi database
                include 'routes/config.php';

                // Ambil data karyawan
                $query = "SELECT * FROM users";
                $result = mysqli_query($conn, $query);

                // Loop data
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)) {
                    // Jika photo kosong, pakai default icon
                    $photo = !empty($row['photo']) ? $row['photo'] : $defaultAvatar;
                ?>
                <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $no++; ?></td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                     <img class="h-10 w-10 rounded-full object-cover object-center" src="<?php echo $photo; ?>" alt="">
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900"><?php echo $row['name']; ?></div>
                        <div class="text-sm text-gray-500"><?php echo $row['email']; ?></div>
                    </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <?php
                        // Set warna berdasarkan role
                        $role = strtolower($row['role']);
                        if ($role == 'superadmin') {
                        $bg = 'bg-blue-100';
                        $text = 'text-blue-800';
                        } elseif ($role == 'admin') {
                        $bg = 'bg-red-100';
                        $text = 'text-red-800';
                        } elseif ($role == 'customer') {
                        $bg = 'bg-yellow-100';
                        $text = 'text-yellow-800';
                        } else {
                        $bg = 'bg-gray-100';
                        $text = 'text-gray-800';
                        }
                    ?>
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo "$bg $text"; ?>">
                        <?php echo ucfirst($row['role']); ?>
                    </span>
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                    Aktif
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="edit.php?id=<?php echo $row['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="resign.php?id=<?php echo $row['id']; ?>" class="text-yellow-600 hover:text-yellow-900 mr-3" onclick="return confirm('Yakin mau resign-kan karyawan ini?')">
                    <i class="fas fa-sign-out-alt"></i>
                    </a>
                    <a href="delete.php?id=<?php echo $row['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin mau hapus?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
                </tr>
                <?php
                }
                ?>
              <!-- Add more rows as needed -->
            </tbody>
          </table>
        </div>
      </div>

      <!-- Pagination -->
      <div class="flex items-center justify-between mt-4">
        <div class="text-sm text-gray-500">
          Menampilkan <span class="font-medium">1</span> sampai <span class="font-medium">10</span> dari <span class="font-medium">100</span> hasil
        </div>
        <div class="flex space-x-2">
          <button class="px-3 py-1 border rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Sebelumnya
          </button>
          <button class="px-3 py-1 border rounded-md text-sm font-medium text-white bg-blue-500 hover:bg-blue-600">
            1
          </button>
          <button class="px-3 py-1 border rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Selanjutnya
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Flowbite JS -->
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

  <style>
    #sidebar {
      transition: transform 0.3s ease-out;
      height: 100vh;
      overflow-y: auto;
      overflow-x: hidden;
    }

    .-translate-x-full {
      transform: translateX(-100%);
    }

    .ml-0 {
      margin-left: 0 !important;
    }

    .ml-64 {
      margin-left: 16rem !important;
    }

    .pl-16 {
      padding-left: 4rem !important;
    }

    #main-content, #navbar {
      transition: margin-left 0.3s ease-out, padding-left 0.3s ease-out;
    }

    .animate-fade-in-down {
      animation: fadeInDown 0.5s ease-out;
    }

    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</body>
</html>