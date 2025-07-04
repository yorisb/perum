<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Jika pakai Composer
// require 'path/to/PHPMailer/src/Exception.php'; // Jika tidak pakai Composer
// require 'path/to/PHPMailer/src/PHPMailer.php';
// require 'path/to/PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = htmlspecialchars($_POST['nama']);
    $email = htmlspecialchars($_POST['email']);
    $pesan = htmlspecialchars($_POST['pesan']);

    // Membuat objek PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Konfigurasi SMTP
        $mail->isSMTP();                                          // Set the mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';                             // Gmail SMTP server
        $mail->SMTPAuth = true;                                    // Enable SMTP authentication
        $mail->Username = 'yorisaepulbarki@gmail.com';             // Email Gmail kamu
        $mail->Password = 'oinurhuda666';                        // Password Gmail kamu
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;        // Enable encryption
        $mail->Port = 587;                                         // TCP port to connect to        

        // Pengaturan pengirim dan penerima
        $mail->setFrom('yorisaepulbarki@gmail.com', 'Unit Perumahan');
        $mail->addAddress('yorisaepulbarki@gmail.com'); // Ganti dengan email yang akan menerima pesan

        // Isi pesan
        $mail->isHTML(true);
        $mail->Subject = 'Pesan dari Kontak Unit Perumahan';
        $mail->Body    = "<h3>Pesan baru dari formulir kontak:</h3><p><strong>Nama:</strong> $nama</p><p><strong>Email:</strong> $email</p><p><strong>Pesan:</strong><br>$pesan</p>";

        // Kirim email
        $mail->send();
        echo 'Pesan berhasil dikirim!';
    } catch (Exception $e) {
        echo "Pesan tidak dapat dikirim. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>
