<?php
session_start();
session_unset(); // menghapus semua session
session_destroy(); // mengakhiri session
header("Location: login.php?message=logout");
exit;
?>
