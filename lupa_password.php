<?php
session_start();

// Cek apakah user sudah login, redirect kalau iya
if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit;
}

// Cek apakah ada pesan sukses atau error
$success = '';
$error = '';

if (!empty($_SESSION['forgot_success'])) {
    $success = $_SESSION['forgot_success'];
    unset($_SESSION['forgot_success']);
}

if (!empty($_SESSION['forgot_error'])) {
    $error = $_SESSION['forgot_error'];
    unset($_SESSION['forgot_error']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Lupa Password - Aplikasi Anda</title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <style>
    .animate-fade-in-down { animation: fade-in-down 0.4s ease-out; }
    @keyframes fade-in-down { 0%{opacity:0;transform:translateY(-10px);} 100%{opacity:1;transform:translateY(0);} }
    .float-animation { animation: float 6s ease-in-out infinite; }
    @keyframes float { 0%,100%{transform:translateY(0);} 50%{transform:translateY(-10px);} }
    .btn-hover-effect { transition: all 0.3s ease; }
    .btn-hover-effect:hover { transform: translateY(-2px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
  </style>
</head>
<body class="min-h-screen flex flex-col justify-between bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');">

  <!-- Floating particles -->
  <div id="particles-js" class="fixed inset-0 z-0"></div>

  <!-- Box -->
  <div class="flex items-center justify-center flex-grow z-10">
    <div class="bg-white bg-opacity-90 p-8 rounded-xl shadow-4xl w-full max-w-sm backdrop-blur-sm transition-all duration-300 border border-gray-100">
      <div class="flex justify-center mb-4">
        <img src="image/logo.ico" alt="Logo" class="h-20 w-20 rounded-full border-4 border-green-200 object-cover float-animation">
      </div>
      <h2 class="text-2xl font-bold text-gray-800 text-center">Reset Password</h2>
      <p class="text-gray-500 text-center mt-1 mb-6">Masukkan email untuk reset password.</p>

        <!-- Compact Success Message -->
        <?php if (!empty($success)): ?>
        <div class="max-w-xs bg-blue-50 border-l-4 border-blue-500 text-blue-800 px-1 py-1 rounded-r-md mb-4 shadow-xs flex items-start gap-2 animate-fade-in">
            <div class="flex-shrink-0 p-1 rounded-full bg-blue-100">
            <svg class="w-2 h-2 text-blue-600" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707-9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
            </svg>
            </div>
            <div class="flex-1">
            <p class="text-sm font-medium leading-tight"><?php echo $success; ?></p>
            </div>
        </div>
        <?php endif; ?>

      <!-- Pesan error -->
      <?php if (!empty($error)): ?>
        <div class="bg-red-500 text-white p-3 rounded-lg mb-4 flex items-center animate-fade-in-down">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p><?php echo $error; ?></p>
        </div>
      <?php endif; ?>

      <form action="routes/forgot_process.php" method="POST" class="space-y-4">
        <div>
          <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
          <input type="email" id="email" name="email" required
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200"
            placeholder="Masukkan email kamu"
            autocomplete="email">
        </div>
        <button type="submit"
          class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition duration-200 btn-hover-effect">
          Kirim Link Reset
        </button>
      </form>

      <p class="mt-6 text-start text-sm text-gray-600">
        Kembali ke <a href="login.php" class="text-blue-500 hover:underline hover:text-blue-700">Login</a>
      </p>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-transparent bg-opacity-90 text-center text-sm text-white py-4 backdrop-blur-sm shadow-md z-10">
    <span class="text-sm text-gray-100 sm:text-center">
      &copy; 2025 <a href="#" class="hover:underline font-semibold text-white">GenIT</a>. All Rights Reserved.
    </span>
  </footer>

  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>
  <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
  <script>
    // Initialize particles
    particlesJS("particles-js", {
      "particles": {
        "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
        "color": { "value": "#ffffff" },
        "shape": { "type": "circle" },
        "opacity": { "value": 0.3 },
        "size": { "value": 3, "random": true },
        "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.2, "width": 1 },
        "move": { "enable": true, "speed": 2 }
      },
      "interactivity": { "events": { "onhover": { "enable": true, "mode": "grab" } } },
      "retina_detect": true
    });
  </script>
</body>
</html>
