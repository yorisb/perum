<?php
session_start();
include 'config.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    die('Token tidak valid.');
}

// Cek token di database
$query = "SELECT * FROM users WHERE reset_token = '$token'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    die('Token tidak valid atau sudah dipakai.');
}

// Proses submit password baru
if (isset($_POST['submit'])) {
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    if (empty($password) || empty($password2)) {
        $error = 'Password wajib diisi.';
    } elseif ($password != $password2) {
        $error = 'Password tidak cocok.';
    } else {
        // Update password + hapus token
        $update = "UPDATE users SET password = '$password', reset_token = NULL WHERE reset_token = '$token'";
        mysqli_query($conn, $update);

        $_SESSION['reset_success'] = 'Password berhasil direset! Silakan login.';
        header('Location: ../login.php');
        exit;
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .float-animation {
            animation: float 3s ease-in-out infinite;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        .btn-hover-effect {
            transition: all 0.3s ease;
            transform: translateY(0);
        }
        .btn-hover-effect:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(74, 222, 128, 0.3);
        }
        .animate-fade-in-down {
            animation: fadeInDown 0.5s ease-out;
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between bg-cover bg-center bg-no-repeat" 
style="background-image: url('https://images.unsplash.com/photo-1521791055366-0d553872125f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80'); font-family: 'Poppins', sans-serif;">

    <!-- Floating particles -->
    <div id="particles-js" class="fixed inset-0 z-0"></div>

    <!-- Main Content -->
    <div class="flex items-center justify-center flex-grow z-10 px-4">
        <div class="bg-white bg-opacity-90 p-8 rounded-2xl shadow-xl w-full max-w-md backdrop-blur-sm border border-white border-opacity-30 transform transition-all duration-300 hover:shadow-2xl">
            <div class="flex flex-col items-center mb-6">
                <img src="../image/logo.ico" alt="Logo" class="h-24 w-24 rounded-full border-4 border-emerald-100 object-cover float-animation shadow-md">
                <h2 class="text-3xl font-bold text-gray-800 mt-4 text-center">Reset Password</h2>
                <p class="text-gray-500 mt-2 text-center">Buat password baru untuk akun Anda</p>
            </div>

            <!-- Success Message -->
            <?php if (!empty($success)): ?>
            <div class="bg-emerald-500 text-white p-4 rounded-lg mb-6 flex items-center animate-fade-in-down shadow-md">
                <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p class="font-medium"><?php echo $success; ?></p>
            </div>
            <?php endif; ?>

            <!-- Error Message -->
            <?php if (!empty($error)): ?>
            <div class="bg-rose-500 text-white p-4 rounded-lg mb-6 flex items-center animate-fade-in-down shadow-md">
                <svg class="w-6 h-6 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="font-medium"><?php echo $error; ?></p>
            </div>
            <?php endif; ?>

            <form method="post" class="space-y-5">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition duration-200 placeholder-gray-400"
                        placeholder="Masukkan password baru"
                        autocomplete="new-password">
                </div>
                
                <div>
                    <label for="password2" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                    <input type="password" id="password2" name="password2" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition duration-200 placeholder-gray-400"
                        placeholder="Ketik ulang password baru"
                        autocomplete="new-password">
                </div>

                <button type="submit" name="submit"
                    class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-semibold py-3.5 rounded-xl transition duration-200 btn-hover-effect shadow-md">
                    Reset Password
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="../login.php" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 hover:underline transition duration-200">
                    ← Kembali ke halaman login
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-black bg-opacity-20 text-center text-sm text-white py-4 backdrop-blur-sm z-10">
        <div class="container mx-auto px-4">
            <span class="text-sm text-white/80">
                &copy; 2025 <a href="#" class="hover:underline font-medium text-white">GenIT</a>. All Rights Reserved.
            </span>
        </div>
    </footer>

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
            "interactivity": { 
                "events": { 
                    "onhover": { 
                        "enable": true, 
                        "mode": "grab" 
                    } 
                } 
            },
            "retina_detect": true
        });
    </script>
</body>
</html>