<?php
session_start();

$success_message = '';
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kontak - Unit Perumahan</title>
  <link rel="icon" href="image/logo.ico" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.css" rel="stylesheet" />
  <script src="https://unpkg.com/flowbite@1.6.5/dist/flowbite.min.js" defer></script>
</head>
<body class="bg-gray-100 min-h-screen">

<?php include 'templates/navbar.php'; ?>

<?php if (!empty($success_message)): ?>
  <div id="successMessage" class="bg-green-600 text-white p-4 rounded-lg shadow-md mb-4 max-w-lg mx-auto mt-4">
    <div class="flex items-center">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M12 9v6m0 0h3m-3 0H9"></path>
      </svg>
      <p class="flex-1"><?php echo $success_message; ?></p>
    </div>
  </div>
<?php endif; ?>

<!-- Kontak Section -->
<section class="max-w-4xl mx-auto mt-10 px-4">
  <h1 class="text-3xl font-bold text-gray-800 text-center mb-6">Hubungi Kami</h1>

    <form action="proses_kontak.php" method="POST" class="bg-white p-6 rounded-lg shadow-md space-y-4">
        <div>
            <label class="block text-gray-700 font-medium">Nama</label>
            <input type="text" name="nama" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Email</label>
            <input type="email" name="email" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div>
            <label class="block text-gray-700 font-medium">Pesan</label>
            <textarea name="pesan" rows="5" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
        </div>

        <div class="text-right">
            <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-medium px-6 py-2 rounded-lg">
                Kirim Pesan
            </button>
        </div>
    </form>
      <!-- Alternatif Kontak -->
      <div class="mt-8 text-center space-y-4">
      <p class="text-gray-700">Atau hubungi kami langsung via:</p>

        <!-- WhatsApp Links -->
        <div class="flex flex-col sm:flex-row justify-center sm:space-x-4 space-y-3 sm:space-y-0">
        <!-- WA 1 -->
        <a href="https://wa.me/6281324240316?text=Halo%2C%20boleh%20minta%20brosur%20unitnya%3F" target="_blank"
            class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2">
            <!-- WhatsApp Icon -->
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 32 32">
            <path d="M19.11 17.37c-.26-.13-1.53-.76-1.77-.85-.24-.1-.41-.13-.58.13-.17.25-.67.85-.82 1.02-.15.17-.3.19-.56.06-.26-.13-1.1-.41-2.09-1.3-.77-.69-1.29-1.53-1.44-1.79-.15-.26-.02-.4.11-.53.11-.11.26-.3.38-.45.13-.15.17-.26.26-.43.08-.17.04-.32-.02-.45-.06-.13-.58-1.4-.8-1.92-.21-.51-.43-.44-.58-.45l-.5-.01c-.17 0-.45.06-.68.32-.23.26-.9.88-.9 2.15 0 1.26.92 2.48 1.05 2.65.13.17 1.8 2.73 4.38 3.82.61.26 1.09.42 1.47.54.62.2 1.18.17 1.62.1.5-.08 1.53-.63 1.75-1.24.22-.61.22-1.13.15-1.24-.06-.11-.24-.17-.5-.3zM16 3C9.38 3 4 8.38 4 15c0 2.64.84 5.08 2.27 7.08L4 29l7.19-2.25C12.91 28.25 14.4 29 16 29c6.62 0 12-5.38 12-12S22.62 3 16 3zM16 26c-1.38 0-2.65-.41-3.71-1.11l-.26-.17-4.26 1.34 1.4-4.14-.18-.27C7.7 20.12 7 17.64 7 15c0-4.97 4.03-9 9-9s9 4.03 9 9-4.03 9-9 9z"/>
            </svg>
            <span>CS 1</span>
        </a>

        <!-- WA 2 -->
        <a href="https://wa.me/6281223542690?text=Halo%2C%20saya%20mau%20konsultasi%20tentang%20unit%20rumahnya." target="_blank"
            class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 32 32">
            <path d="M19.11 17.37c-.26-.13-1.53-.76-1.77-.85-.24-.1-.41-.13-.58.13-.17.25-.67.85-.82 1.02-.15.17-.3.19-.56.06-.26-.13-1.1-.41-2.09-1.3-.77-.69-1.29-1.53-1.44-1.79-.15-.26-.02-.4.11-.53.11-.11.26-.3.38-.45.13-.15.17-.26.26-.43.08-.17.04-.32-.02-.45-.06-.13-.58-1.4-.8-1.92-.21-.51-.43-.44-.58-.45l-.5-.01c-.17 0-.45.06-.68.32-.23.26-.9.88-.9 2.15 0 1.26.92 2.48 1.05 2.65.13.17 1.8 2.73 4.38 3.82.61.26 1.09.42 1.47.54.62.2 1.18.17 1.62.1.5-.08 1.53-.63 1.75-1.24.22-.61.22-1.13.15-1.24-.06-.11-.24-.17-.5-.3zM16 3C9.38 3 4 8.38 4 15c0 2.64.84 5.08 2.27 7.08L4 29l7.19-2.25C12.91 28.25 14.4 29 16 29c6.62 0 12-5.38 12-12S22.62 3 16 3zM16 26c-1.38 0-2.65-.41-3.71-1.11l-.26-.17-4.26 1.34 1.4-4.14-.18-.27C7.7 20.12 7 17.64 7 15c0-4.97 4.03-9 9-9s9 4.03 9 9-4.03 9-9 9z"/>
            </svg>
            <span>CS 2</span>
        </a>

        <!-- WA 3 -->
        <a href="https://wa.me/62895361639529?text=Halo%2C%20saya%20tertarik%20dengan%20unit%20perumahan%20Anda." target="_blank"
            class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center justify-center space-x-2">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 32 32">
            <path d="M19.11 17.37c-.26-.13-1.53-.76-1.77-.85-.24-.1-.41-.13-.58.13-.17.25-.67.85-.82 1.02-.15.17-.3.19-.56.06-.26-.13-1.1-.41-2.09-1.3-.77-.69-1.29-1.53-1.44-1.79-.15-.26-.02-.4.11-.53.11-.11.26-.3.38-.45.13-.15.17-.26.26-.43.08-.17.04-.32-.02-.45-.06-.13-.58-1.4-.8-1.92-.21-.51-.43-.44-.58-.45l-.5-.01c-.17 0-.45.06-.68.32-.23.26-.9.88-.9 2.15 0 1.26.92 2.48 1.05 2.65.13.17 1.8 2.73 4.38 3.82.61.26 1.09.42 1.47.54.62.2 1.18.17 1.62.1.5-.08 1.53-.63 1.75-1.24.22-.61.22-1.13.15-1.24-.06-.11-.24-.17-.5-.3zM16 3C9.38 3 4 8.38 4 15c0 2.64.84 5.08 2.27 7.08L4 29l7.19-2.25C12.91 28.25 14.4 29 16 29c6.62 0 12-5.38 12-12S22.62 3 16 3zM16 26c-1.38 0-2.65-.41-3.71-1.11l-.26-.17-4.26 1.34 1.4-4.14-.18-.27C7.7 20.12 7 17.64 7 15c0-4.97 4.03-9 9-9s9 4.03 9 9-4.03 9-9 9z"/>
            </svg>
            <span>CS 3</span>
        </a>
        </div>
    </div>
</section>


<?php include 'templates/footer.php'; ?>

</body>
</html>
