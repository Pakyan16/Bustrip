<?php
session_start();
include 'koneksi.php';

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$jadwal_id = $_POST['jadwal_id'];
$kursi_id  = $_POST['kursi_id'];
$user_id   = $_SESSION['user_id'];

// Ambil penumpang_id
$getPen = $conn->query("SELECT penumpang_id FROM penumpang WHERE user_id='$user_id'");
$pen = $getPen->fetch_assoc();
$penumpang_id = $pen['penumpang_id'];

// Cek apakah kursi sudah terisi
$cekKursi = $conn->query("SELECT status FROM kursi WHERE kursi_id='$kursi_id'");
$dataKursi = $cekKursi->fetch_assoc();

if ($dataKursi['status'] == "Terisi") {
    echo "<script>alert('Kursi sudah terisi! Pilih kursi lain.'); window.location='pilih_kursi.php?jadwal_id=$jadwal_id';</script>";
    exit;
}

// Tandai kursi jadi terisi
$conn->query("UPDATE kursi SET status='Terisi' WHERE kursi_id='$kursi_id'");

// Simpan pemesanan
$tanggal_pesan = date("Y-m-d");
$status_pesanan = "Menunggu Pembayaran";

$conn->query("INSERT INTO pemesanan_tiket 
    (penumpang_id, jadwal_id, kursi_id, tanggal_pesan, status_pesanan)
    VALUES ('$penumpang_id', '$jadwal_id', '$kursi_id', '$tanggal_pesan', '$status_pesanan')
");

echo "<script>
    alert('Tiket berhasil dipesan!');
    window.location='dashboard_penumpang.php';
</script>";
?>
