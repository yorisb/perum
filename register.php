<?php
session_start();

// Kalau user sudah login, lempar ke dashboard
if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit;
}

// Ambil error message kalau ada
$error = '';
if (!empty($_SESSION['register_error'])) {
    $error = $_SESSION['register_error'];
    unset($_SESSION['register_error']);
}

// Ambil success message kalau ada
$success = '';
if (!empty($_SESSION['register_success'])) {
    $success = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register - Aplikasi Anda</title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon" />
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
</head>
<body class="min-h-screen flex items-center justify-center bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');">

  <div class="bg-white bg-opacity-90 p-8 rounded-xl shadow-lg w-full max-w-md backdrop-blur-sm">
    <h2 class="text-center text-3xl font-semibold text-gray-800 mb-6">Daftar Akun</h2>

    <!-- Notifikasi sukses -->
    <?php if (!empty($success)): ?>
      <div class="bg-green-500 text-white p-3 rounded-lg mb-4">
        <p><?php echo $success; ?></p>
      </div>
    <?php endif; ?>

    <!-- Notifikasi error -->
    <?php if (!empty($error)): ?>
      <div class="bg-red-500 text-white p-3 rounded-lg mb-4">
        <p><?php echo $error; ?></p>
      </div>
    <?php endif; ?>

    <form action="routes/register_process.php" method="POST" class="space-y-4">
      <div>
        <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
        <input type="text" name="username" id="username" required
               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
      </div>
      <div>
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" id="password" required
               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
      </div>
      <div>
        <label for="confirm_password" class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required
               class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" />
      </div>
      <button type="submit" 
              class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition duration-200">
        Daftar
      </button>
    </form>

    <p class="mt-4 text-center text-sm text-gray-600">
      Sudah punya akun? <a href="login.php" class="text-blue-500 hover:underline">Login di sini</a>
    </p>
  </div>

  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>
</body>
</html>
