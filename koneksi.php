<?php
// Konfigurasi database InfinityFree
$host = "localhost";
$user = "u936058994_bustrip_pakyan";
$pass = "Pakyan_161105";
$db   = "u936058994_bustrip";

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
