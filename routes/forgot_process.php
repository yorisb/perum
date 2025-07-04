<?php
session_start();
include 'config.php'; // koneksi database

$email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');

if (empty($email)) {
    $_SESSION['forgot_error'] = 'Email wajib diisi.';
    header('Location: ../lupa_password.php');
    exit;
}

$query = "SELECT * FROM users WHERE email = '$email'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) > 0) {
    // ✔️ Email ditemukan
    $token = bin2hex(random_bytes(16)); // generate token unik

    // Simpan token ke database
    $update = "UPDATE users SET reset_token = '$token' WHERE email = '$email'";
    mysqli_query($conn, $update);

    // Buat link reset password
    $link = "routes/reset_password.php?token=$token";

    // (Sementara) tampilkan link reset (belum dikirim email)
    $_SESSION['forgot_success'] = "Link reset: <a href='$link'>$link</a>";
} else {
    $_SESSION['forgot_error'] = 'Email tidak terdaftar!';
}

header('Location: ../lupa_password.php');
exit;
?>
