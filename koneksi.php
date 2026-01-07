<?php
// Konfigurasi database InfinityFree
$host = "sql110.byetcluster.com";
$user = "if0_40700535";
$pass = "Your vPanel Password";
$db   = "if0_40700535_db_bustrip";

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
