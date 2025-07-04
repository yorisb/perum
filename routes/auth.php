<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);

    if ($stmt) {
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $user = $result->fetch_assoc()) {
            // Verifikasi password (tanpa hash)
            if ($password === $user['password']) {
                $_SESSION['id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                $_SESSION['success_message'] = 'Login berhasil! Selamat datang, ' . $user['username'] . '.';
                header("Location: ../dashboard.php");
                exit;
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Username tidak ditemukan!";
        }

        $stmt->close();
    } else {
        $error = "Terjadi kesalahan pada query: " . $conn->error;
    }

    $_SESSION['login_error'] = $error;
    header("Location: ../login.php");
    exit;
}
?>
