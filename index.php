<?php
session_start();

// Cek apakah user sudah login, redirect kalau iya
if (isset($_SESSION['username'])) {
    header('Location: dashboard.php');
    exit;
}

// Cek apakah ada pesan error dari proses login
$error = '';
if (!empty($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Hapus error biar nggak muncul terus
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Aplikasi Anda</title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <style>
    @keyframes fade-in-down {
      0% {
        opacity: 0;
        transform: translateY(-10px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
    .animate-fade-in-down {
      animation: fade-in-down 0.4s ease-out;
    }
    
    /* New animations */
    @keyframes float {
      0%, 100% {
        transform: translateY(0);
      }
      50% {
        transform: translateY(-10px);
      }
    }
    .float-animation {
      animation: float 6s ease-in-out infinite;
    }
    
    @keyframes pulse {
      0%, 100% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.05);
      }
    }
    .pulse-animation {
      animation: pulse 2s ease-in-out infinite;
    }
    
    .smooth-transition {
      transition: all 0.3s ease;
    }
    
    .password-toggle {
      cursor: pointer;
      transition: all 0.2s ease;
    }
    .password-toggle:hover {
      transform: scale(1.1);
    }
    
    .btn-hover-effect {
      transition: all 0.3s ease;
    }
    .btn-hover-effect:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
  </style>  
</head>
<body class="min-h-screen flex flex-col justify-between bg-cover bg-center bg-no-repeat" style="background-image: url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1600&q=80');">

  <!-- Floating particles background -->
  <div id="particles-js" class="fixed inset-0 z-0"></div>

  <!-- Message Logout -->
  <?php if (isset($_GET['message']) && $_GET['message'] === 'logout'): ?>
    <div id="logout-alert" class="auto-dismiss fixed top-6 left-1/2 transform -translate-x-1/2 bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded-md shadow-md flex items-center space-x-2 text-sm animate-fade-in-down z-50">
      <!-- Icon -->
      <svg class="w-5 h-5 text-green-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H6a2 2 0 01-2-2V7a2 2 0 012-2h5a2 2 0 012 2v1" />
      </svg>
      <!-- Text -->
      <div>
        <p class="font-semibold">Berhasil logout!</p>
        <p class="text-xs">Silakan login kembali.</p>
      </div>
    </div>
  <?php endif; ?>

  <!-- Login Box -->
  <div class="flex items-center justify-center flex-grow z-10">
    <div id="loginBox" class="bg-white bg-opacity-90 p-8 rounded-xl shadow-4xl w-full max-w-sm backdrop-blur-sm smooth-transition hover:shadow-4xl hover:-translate-y-1 transition-all duration-300 border border-gray-100">
      <div class="flex justify-center mb-4">
        <img src="image/logo.ico" alt="Logo" class="h-20 w-20 rounded-full border-4 border-green-200 object-cover float-animation" id="logoImage">
      </div>
      <h2 class="text-2xl font-bold text-gray-800 text-center">Perum Name</h2>
      <p class="text-gray-500 text-center mt-1 mb-6">Silahkan login terlebih dahulu.</p>

      <!-- Tampilkan pesan error jika ada -->
      <?php if (!empty($error)): ?>
        <div id="errorAlert" class="auto-dismiss bg-red-500 text-white p-3 rounded-lg mb-4 transition-opacity duration-500 flex items-center">
          <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p><?php echo $error; ?></p>
        </div>
      <?php endif; ?>

      <!-- Success Message -->
      <?php if (isset($_SESSION['reset_success'])): ?>
          <div id="success-message" class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-md mb-4 flex items-start gap-2">
              <div class="flex-1"><?php echo $_SESSION['reset_success']; ?></div>
          </div>

          <script>
              setTimeout(function() {
                  document.getElementById('success-message').style.display = 'none';
              }, 3000); // Hide after 3 seconds
          </script>
          
          <?php unset($_SESSION['reset_success']); ?>
      <?php endif; ?>


      <form action="routes/auth.php" method="POST" class="space-y-4" id="loginForm">
        <div>
          <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
          <input type="text" id="username" name="username" required 
             class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200" 
             placeholder="Enter your username"
             autocomplete="username">
        </div>
        <div class="relative">
          <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
          <input type="password" id="password" name="password" required 
             class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition duration-200 pr-10" 
             placeholder="••••••••"
             autocomplete="current-password">
          <button type="button" id="togglePassword" class="absolute right-3 bottom-3 text-gray-500 hover:text-gray-700 focus:outline-none password-toggle">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="eyeIcon">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            </svg>
          </button>
        </div>
        <div class="flex items-center justify-between">
          <div class="flex items-center">
              <!--  -->
          </div>
          <div class="text-sm">
            <a href="lupa_password.php" class="font-medium text-green-600 hover:text-green-500">Lupa password?</a>
          </div>
        </div>
        <button type="submit" 
                class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded-lg transition duration-200 btn-hover-effect pulse-animation">
          Login
        </button>
      </form>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-transparent bg-opacity-90 text-center text-sm text-white py-4 backdrop-blur-sm shadow-md z-10">
    <span class="text-sm text-gray-100 sm:text-center">
      &copy; 2025 <a href="#" class="hover:underline font-semibold text-white smooth-transition">GenIT</a>. All Rights Reserved.
    </span>
  </footer>

  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js"></script>
  <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
  
  <script>
    document.addEventListener("DOMContentLoaded", function() {
      // Auto-dismiss alerts
      const alerts = document.querySelectorAll('.auto-dismiss');
      alerts.forEach(alert => {
        setTimeout(() => {
          alert.classList.add('opacity-0', 'transition-opacity', 'duration-500');
          setTimeout(() => alert.remove(), 500);
        }, 5000);
      });
      
      // Password toggle functionality
      const togglePassword = document.getElementById('togglePassword');
      const passwordInput = document.getElementById('password');
      const eyeIcon = document.getElementById('eyeIcon');
      
      if (togglePassword && passwordInput) {
        togglePassword.addEventListener('click', function() {
          const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
          passwordInput.setAttribute('type', type);
          
          // Toggle eye icon
          if (type === 'text') {
            eyeIcon.innerHTML = `
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
            `;
          } else {
            eyeIcon.innerHTML = `
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
            `;
          }
        });
      }
      
      // Form submission animation
      const loginForm = document.getElementById('loginForm');
      if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
          const submitButton = this.querySelector('button[type="submit"]');
          if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = `
              <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Processing...
            `;
          }
        });
      }
      
      // Initialize particles.js
      if (typeof particlesJS !== 'undefined') {
        particlesJS("particles-js", {
          "particles": {
            "number": {
              "value": 80,
              "density": {
                "enable": true,
                "value_area": 800
              }
            },
            "color": {
              "value": "#ffffff"
            },
            "shape": {
              "type": "circle",
              "stroke": {
                "width": 0,
                "color": "#000000"
              },
              "polygon": {
                "nb_sides": 5
              }
            },
            "opacity": {
              "value": 0.3,
              "random": false,
              "anim": {
                "enable": false,
                "speed": 1,
                "opacity_min": 0.1,
                "sync": false
              }
            },
            "size": {
              "value": 3,
              "random": true,
              "anim": {
                "enable": false,
                "speed": 40,
                "size_min": 0.1,
                "sync": false
              }
            },
            "line_linked": {
              "enable": true,
              "distance": 150,
              "color": "#ffffff",
              "opacity": 0.2,
              "width": 1
            },
            "move": {
              "enable": true,
              "speed": 2,
              "direction": "none",
              "random": false,
              "straight": false,
              "out_mode": "out",
              "bounce": false,
              "attract": {
                "enable": false,
                "rotateX": 600,
                "rotateY": 1200
              }
            }
          },
          "interactivity": {
            "detect_on": "canvas",
            "events": {
              "onhover": {
                "enable": true,
                "mode": "grab"
              },
              "onclick": {
                "enable": true,
                "mode": "push"
              },
              "resize": true
            },
            "modes": {
              "grab": {
                "distance": 140,
                "line_linked": {
                  "opacity": 1
                }
              },
              "bubble": {
                "distance": 400,
                "size": 40,
                "duration": 2,
                "opacity": 8,
                "speed": 3
              },
              "repulse": {
                "distance": 200,
                "duration": 0.4
              },
              "push": {
                "particles_nb": 4
              },
              "remove": {
                "particles_nb": 2
              }
            }
          },
          "retina_detect": true
        });
      }
      
      // Add floating animation to login box on hover
      const loginBox = document.getElementById('loginBox');
      if (loginBox) {
        loginBox.addEventListener('mouseenter', () => {
          loginBox.classList.add('transform', 'transition-transform', 'duration-300');
        });
        
        loginBox.addEventListener('mouseleave', () => {
          loginBox.classList.remove('transform', 'transition-transform', 'duration-300');
        });
      }
          // Cek jika ada cookie remember me dan isi form
      if (document.cookie.includes('remember_token')) {
          // Anda mungkin perlu request ke server untuk mendapatkan username
          // Ini contoh sederhana, sebaiknya gunakan AJAX request
          const rememberCheckbox = document.getElementById('remember-me');
          if (rememberCheckbox) {
              rememberCheckbox.checked = true;
          }
      }
    });
  </script>
</body>
</html>