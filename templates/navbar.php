<?php
$defaultAvatar = 'data:image/svg+xml;base64,' . base64_encode(
  '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
    <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275M15 9.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
  </svg>
  '
);
?>

<!-- Navbar -->
<nav class="bg-white shadow-md sticky top-0 z-50">
  <div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center h-16">
      
      <!-- Logo - Kiri -->
      <div class="flex-shrink-0">
        <a href="/" class="text-2xl font-bold text-gray-800 tracking-wide no-underline">🏡 Unit Perumahan</a>
      </div>

      <!-- Navigation Links - Kanan -->
      <div class="hidden md:flex items-center space-x-4">
      <?php if (isset($_SESSION['username'])): ?>
      <a href="tentangkami.php" class="text-gray-700 hover:text-green-600 font-medium mr-4">
          About
      </a>
      <?php endif; ?>

        <!-- Profile/Login -->
        <div class="relative ml-4">
          <?php if (isset($_SESSION['username'])): ?>
            <button id="profile-button" class="flex items-center space-x-2 focus:outline-none">
            <span class="text-gray-700 font-medium"><?= htmlspecialchars($_SESSION['username']); ?></span>
              <img 
                class="w-8 h-8 rounded-full object-cover" 
                src="<?= !empty($user['photo']) ? htmlspecialchars($user['photo']) : $defaultAvatar; ?>" 
                alt="user photo"
              />
              <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            <div id="profile-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-2 z-50">
              <a href="dashboard.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Dashboard</a>
              <a href="profil.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Profil Saya</a>
              <a href="logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">Logout</a>
            </div>
          <?php else: ?>
            <a href="login.php" class="text-gray-700 hover:text-green-600 font-medium">Login</a>
          <?php endif; ?>
        </div>
      </div>

      <!-- Mobile Menu Button -->
      <div class="md:hidden flex items-center">
        <button id="mobile-menu-button" class="text-gray-700 hover:text-green-600 focus:outline-none">
          <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
          </svg>
        </button>
      </div>
    </div>
  </div>

  <!-- Mobile Menu -->
  <div id="mobile-menu" class="md:hidden hidden px-4 pb-4">
    <div class="pt-2 pb-3 space-y-1">
      <a href="tentang-kami.php" class="block px-3 py-2 text-gray-700 hover:text-green-600 font-medium">About</a>
      <?php if (isset($_SESSION['username'])): ?>
        <a href="dashboard.php" class="block px-3 py-2 text-gray-700 hover:text-green-600 font-medium">Dashboard</a>
        <a href="profil.php" class="block px-3 py-2 text-gray-700 hover:text-green-600 font-medium">Profil Saya</a>
        <a href="logout.php" class="block px-3 py-2 text-red-600 hover:text-red-700 font-medium">Logout</a>
      <?php else: ?>
        <a href="login.php" class="block px-3 py-2 text-gray-700 hover:text-green-600 font-medium">Login</a>
      <?php endif; ?>
    </div>
  </div>
</nav>

<script>
  // Toggle mobile menu
  const menuButton = document.getElementById('mobile-menu-button');
  const mobileMenu = document.getElementById('mobile-menu');

  menuButton.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
  });

  // Toggle profile dropdown
  const profileButton = document.getElementById('profile-button');
  const profileDropdown = document.getElementById('profile-dropdown');

  if (profileButton) {
    profileButton.addEventListener('click', (e) => {
      e.stopPropagation();
      profileDropdown.classList.toggle('hidden');
    });
  }

  // Close dropdown when clicking outside
  window.addEventListener('click', (e) => {
    if (profileDropdown && !profileDropdown.classList.contains('hidden') && 
        !profileButton.contains(e.target) && !profileDropdown.contains(e.target)) {
      profileDropdown.classList.add('hidden');
    }
  });
</script>