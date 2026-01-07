<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['pemesanan_id'])) {
    echo "Tiket tidak ditemukan!";
    exit;
}

$pemesanan_id = intval($_GET['pemesanan_id']);

$sql = $conn->query("SELECT pt.*, pt.nomor_kursi, j.asal, j.tujuan, j.tanggal, j.jam, j.harga, 
                     u.nama, u.email
                     FROM pemesanan_tiket pt
                     JOIN penumpang p ON pt.penumpang_id = p.penumpang_id
                     JOIN user u ON p.user_id = u.user_id
                     JOIN jadwal_keberangkatan j ON pt.jadwal_id = j.jadwal_id
                     WHERE pt.pemesanan_id = '$pemesanan_id'");

$tiket = $sql->fetch_assoc();

if (!$tiket) { echo "Tiket tidak ditemukan!"; exit; }
if ($tiket['status_pembayaran'] != "LUNAS") {
    echo "<h3 style='color:red;text-align:center;margin-top:40px;'>❌ Pembayaran belum LUNAS.</h3>";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>E-Tiket | BusTrip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; font-family: Poppins, sans-serif; }
        .ticket-card {
            background: white;
            border-radius: 18px;
            padding: 25px;
            border: 2px dashed #ff9800;
            max-width: 650px;
            margin: auto;
            margin-top: 40px;
        }
        .line { border-bottom: 1px dashed #ccc; margin: 15px 0; }
        .qr {
            width: 150px;
            border: 3px solid #ddd;
            padding: 5px;
            border-radius: 10px;
        }
    </style>
</head>
<body>
<div class="ticket-card shadow">
    <h3 class="text-center fw-bold text-warning">🎫 E-Tiket BusTrip</h3>
    <p class="text-center text-muted">Tunjukkan tiket ini saat naik bus</p>
    <div class="line"></div>

    <div class="row">
        <div class="col-md-7">
            <h5 class="fw-bold"><?= $tiket['asal'] ?> → <?= $tiket['tujuan'] ?></h5>
            <p class="mb-1"><strong>Nama:</strong> <?= $tiket['nama'] ?></p>
            <p class="mb-1"><strong>Email:</strong> <?= $tiket['email'] ?></p>
            <p class="mb-1"><strong>Tanggal:</strong> <?= $tiket['tanggal'] ?></p>
            <p class="mb-1"><strong>Jam:</strong> <?= $tiket['jam'] ?></p>
            <p class="mb-1"><strong>Nomor Kursi:</strong> <?= $tiket['nomor_kursi'] ?></p>
            <p class="mb-1"><strong>Harga:</strong> Rp <?= number_format($tiket['harga'],0,',','.') ?></p>
            <p class="mt-3"><strong>Status:</strong> <span class="badge bg-success">LUNAS</span></p>
            <p><strong>ID Pemesanan:</strong> <?= $tiket['pemesanan_id'] ?></p>
        </div>

        <div class="col-md-5 text-center">
            <?php $qrURL = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=PEMESANAN_".$tiket['pemesanan_id']; ?>
            <img src="<?= $qrURL ?>" class="qr" alt="QR Code">
            <p class="text-muted mt-1">Scan untuk verifikasi</p>
        </div>
    </div>

    <div class="line"></div>
    <div class="text-center">
        <button onclick="window.print()" class="btn btn-warning px-4 fw-bold">🖨 Cetak / Simpan PDF</button>
    </div>
</div>
</body>
</html>