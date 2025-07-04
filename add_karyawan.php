<?php
session_start();

// Check if user is logged in and has admin role
if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'superadmin' && $_SESSION['role'] !== 'admin')) {
    header("Location: login.php");
    exit();
}

include 'routes/config.php';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $user_name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Validate inputs
    if (empty($name) || empty($user_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Semua field harus diisi!";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok!";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter!";
    } else {
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $user_name, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Username atau email sudah digunakan!";
        } else {
            // Insert new user with plain text password (TIDAK AMAN!)
            $stmt = $conn->prepare("INSERT INTO users (name, username, email, password, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $user_name, $email, $password, $role);

            if ($stmt->execute()) {
                $success = "Karyawan berhasil ditambahkan!";
                // Clear form fields
                $name = $user_name = $email = $role = '';
            } else {
                $error = "Gagal menambahkan karyawan: " . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Tambah Karyawan - Unit Perumahan</title>
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
    <div id="navbar" class="rounded-md fixed top-0 left-0 w-full z-10 transition-all duration-300 ease-out ml-64">
      <?php include 'templates/navbar.php'; ?>
    </div>

        <div class="mt-20 mx-auto px-4">
            <h1 class="text-2xl font-extrabold text-gray-800 mb-6">Tambah Karyawan</h1>

            <!-- Error Message -->
            <?php if (!empty($error)): ?>
                <div id="errorMessage" class="max-w-md mx-auto bg-red-50 border border-red-400 text-red-700 px-6 py-4 rounded-lg mb-6 text-center transition-opacity duration-500">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Success Message -->
            <?php if (!empty($success)): ?>
                <div id="successMessage" class="max-w-md mx-auto bg-green-50 border border-green-400 text-green-700 px-6 py-4 rounded-lg mb-6 text-center transition-opacity duration-500">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <!-- Script untuk auto-hide pesan dengan fade out -->
            <script>
                // Fungsi fade out
                function fadeOut(element) {
                    element.style.opacity = 1;
                    let fadeEffect = setInterval(() => {
                        if (element.style.opacity > 0) {
                            element.style.opacity -= 0.1;
                        } else {
                            clearInterval(fadeEffect);
                            element.style.display = 'none';
                        }
                    }, 50); // Semakin kecil, semakin halus
                }

                // Jalankan setelah 3 detik
                setTimeout(() => {
                    const errorBox = document.getElementById('errorMessage');
                    if (errorBox) fadeOut(errorBox);

                    const successBox = document.getElementById('successMessage');
                    if (successBox) fadeOut(successBox);
                }, 3000);
            </script>

            <!-- Form -->
            <div class="bg-white shadow-xl rounded-2xl p-8">
                <form action="add_karyawan.php" method="POST" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nama Lengkap</label>
                            <input type="text" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <!-- Username -->
                        <div>
                            <label for="username" class="block text-sm font-semibold text-gray-700 mb-2">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo isset($user_name) ? htmlspecialchars($user_name) : ''; ?>" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                            <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                        </div>

                        <!-- Role -->
                        <div>
                            <label for="role" class="block text-sm font-semibold text-gray-700 mb-2">Role</label>
                            <select id="role" name="role" class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="" selected disabled hidden>Silahkan pilih role</option>
                                <?php
                                $roles_query = $conn->query("SELECT DISTINCT role FROM users WHERE role != 'superadmin'");                                
                                if ($roles_query && $roles_query->num_rows > 0) {
                                    while ($role_data = $roles_query->fetch_assoc()) {
                                        $role_value = $role_data['role'];
                                        $selected = (isset($role) && $role === $role_value) ? 'selected' : '';
                                        echo "<option value='$role_value' $selected>" . ucfirst($role_value) . "</option>";
                                    }
                                } else {
                                    echo "<option value='admin' " . ((isset($role) && $role === 'admin') ? 'selected' : '') . ">Admin</option>";
                                    echo "<option value='customer' " . ((isset($role) && $role === 'customer') ? 'selected' : '') . ">Customer</option>";
                                }
                                ?>
                            </select>
                        </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-2">Password</label>
                        <div class="relative">
                            <input type="password" id="password" name="password" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <button type="button" onclick="togglePassword('password', this)" 
                                class="absolute right-3 top-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                                <!-- Eye Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                                    viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="confirm_password" class="block text-sm font-semibold text-gray-700 mb-2">Konfirmasi Password</label>
                        <div class="relative">
                            <input type="password" id="confirm_password" name="confirm_password" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                            <button type="button" onclick="togglePassword('confirm_password', this)" 
                                class="absolute right-3 top-3 text-gray-500 hover:text-gray-700 focus:outline-none">
                                <!-- Eye Icon -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                                    viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                    <!-- Submit Button -->
                    <div class="flex items-center justify-end space-x-4 pt-4">
                        <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                            Tambah Karyawan
                        </button>
                        <a href="karyawan.php" class="px-6 py-3 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                            Kembali
                        </a>
                    </div>
                </form>
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

<script>
function togglePassword(id, btn) {
    const input = document.getElementById(id);
    const svg = btn.querySelector('svg');

    if (input.type === "password") {
        input.type = "text";
        // Ganti ke icon mata silang (eye-off)
        svg.innerHTML = `
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.96 
                   9.96 0 012.293-3.95M6.634 6.634A9.956 9.956 0 0112 5c4.477 
                   0 8.268 2.943 9.542 7a9.973 9.973 0 01-4.21 5.442M15 
                   12a3 3 0 11-6 0 3 3 0 016 0zM3 3l18 18" />
        `;
    } else {
        input.type = "password";
        // Ganti ke icon mata biasa (eye)
        svg.innerHTML = `
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 
                   2.943 9.542 7-1.274 4.057-5.065 7-9.542 
                   7-4.477 0-8.268-2.943-9.542-7z" />
        `;
    }
}
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