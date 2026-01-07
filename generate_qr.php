<?php
require 'phpqrcode/qrlib.php';
include 'koneksi.php';

if (!isset($_GET['pemesanan_id'])) {
    die("ID pemesanan tidak ditemukan.");
}

$id = $_GET['pemesanan_id'];

$q = $conn->query("SELECT * FROM pemesanan_tiket WHERE pemesanan_id = '$id'");
$data = $q->fetch_assoc();

if (!$data) {
    die("Data pesanan tidak ditemukan.");
}

// Cek folder qrcode
if (!file_exists('qrcode')) {
    mkdir('qrcode', 0777, true);
}

$jumlah = 50000;
$nama   = $data['penumpang_id'];

// QR DANA
$qr_content = "https://link.dana.id/qr/123456789?amount=$jumlah";

$filename = "qr_$id.png";
$filepath = "qrcode/" . $filename;

QRcode::png($qr_content, $filepath, QR_ECLEVEL_H, 8);
?>
<!DOCTYPE html>
<html>
<head>
    <title>QR Pembayaran</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .qr-card {
            width: 380px;
            background: #fff;
            padding: 25px;
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
            text-align: center;
            animation: fadeIn 0.4s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .qr-logo {
            width: 130px;
            margin-bottom: 8px;
        }

        h2 {
            color: #0066ff;
            margin-bottom: 6px;
            font-size: 24px;
        }

        p.subtitle {
            color: #555;
            margin-bottom: 18px;
        }

        .qr-box {
            background: #f3f7ff;
            padding: 18px;
            border-radius: 12px;
            border: 1px solid #dce5ff;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .qr-box img {
            width: 230px;
            height: 230px;
        }

        .info-box {
            margin-top: 18px;
            text-align: left;
            background: #eef4ff;
            padding: 15px;
            border-radius: 12px;
            border: 1px solid #d6e0ff;
            font-size: 15px;
        }

        .info-box b {
            color: #003fbe;
        }

        .pay-btn {
            margin-top: 18px;
            width: 100%;
            background: #0066ff;
            padding: 14px;
            color: #fff;
            border-radius: 12px;
            display: block;
            text-decoration: none;
            font-size: 18px;
            font-weight: 600;
            transition: 0.2s;
        }

        .pay-btn:hover {
            background: #004bd6;
        }
    </style>
</head>
<body>

<div class="qr-card">

    <img class="qr-logo"
         src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6f/Logo_dana_blue.svg/512px-Logo_dana_blue.svg.png">

    <h2>QR Pembayaran DANA</h2>
    <p class="subtitle">Silakan scan menggunakan aplikasi DANA</p>

    <div class="qr-box">
        <img src="qrcode/<?= $filename ?>">
    </div>

    <div class="info-box">
        <b>Nama:</b> Penumpang #<?= $data['penumpang_id'] ?><br>
        <b>Total Pembayaran:</b> Rp <?= number_format($jumlah,0,',','.') ?><br>
        <b>ID Pembayaran:</b> <?= $id ?>
    </div>

    <a class="pay-btn" href="<?= $qr_content ?>">Bayar Sekarang</a>

</div>

</body>
</html>
