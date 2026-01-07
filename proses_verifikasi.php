<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $pemesanan_id = $_POST['pemesanan_id'];
    $aksi = $_POST['aksi'];

    if ($aksi == "SETUJU") {
        $query = $conn->query("
            UPDATE pemesanan_tiket
            SET status_pembayaran = 'LUNAS'
            WHERE pemesanan_id = '$pemesanan_id'
        ");
    } 
    else if ($aksi == "TOLAK") {
        $query = $conn->query("
            UPDATE pemesanan_tiket
            SET status_pembayaran = 'DITOLAK'
            WHERE pemesanan_id = '$pemesanan_id'
        ");
    }

    if ($query) {
        header("Location: verifikasi_pembayaran.php?status=berhasil");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }

} else {
    header("Location: dashboard_admin.php");
    exit;
}
?>
