<?php
$va     = $_GET['va'];
$nama   = $_GET['nama'];
$jumlah = $_GET['jumlah'];
$id     = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pembayaran BRIVA</title>

<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f2f6ff;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        padding-top: 40px;
    }

    .container {
        width: 95%;
        max-width: 420px;
        background: white;
        padding: 25px;
        border-radius: 18px;
        box-shadow: 0 5px 18px rgba(0,0,0,0.1);
        animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
        from {opacity: 0; transform: translateY(10px);}
        to {opacity: 1; transform: translateY(0);}
    }

    h2 {
        text-align: center;
        margin-bottom: 15px;
        color: #1a237e;
        font-weight: bold;
    }

    .label {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 6px;
        color: #3949ab;
    }

    .value-box {
        background: #e8eaf6;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 18px;
        margin-bottom: 20px;
        font-weight: 600;
        letter-spacing: 1px;
        color: #1a237e;
    }

    button {
        width: 100%;
        background: #3949ab;
        padding: 14px;
        border: none;
        border-radius: 12px;
        font-size: 17px;
        font-weight: bold;
        color: white;
        cursor: pointer;
        transition: 0.2s;
    }

    button:hover {
        background: #1e2f86;
        transform: scale(1.01);
    }

    .info {
        margin-top: 20px;
        font-size: 14px;
        text-align: center;
        color: #555;
    }

    .steps {
        margin-top: 25px;
        background: #f5f7ff;
        padding: 18px;
        border-radius: 14px;
        border-left: 4px solid #3949ab;
    }

    .steps h3 {
        margin-top: 0;
        color: #1a237e;
    }

    .steps ul {
        padding-left: 18px;
    }

    .steps ul li {
        margin-bottom: 10px;
    }
</style>

<script>
function copyVA() {
    navigator.clipboard.writeText("<?= $va ?>");
    alert("Nomor BRIVA berhasil disalin!");
}
</script>

</head>
<body>

<div class="container">

    <h2>Pembayaran BRIVA</h2>

    <div class="label">Nomor BRIVA</div>
    <div class="value-box" id="vaBox"><?= $va ?></div>

    <button onclick="copyVA()">Salin Nomor BRIVA</button>

    <br><br>

    <div class="label">Nama Pemilik</div>
    <div class="value-box"><?= urldecode($nama) ?></div>

    <div class="label">Total Pembayaran</div>
    <div class="value-box">Rp <?= number_format($jumlah,0,',','.') ?></div>

    <div class="steps">
        <h3>Cara Membayar</h3>
        <ul>
            <li>Buka aplikasi <b>BRImo / ATM BRI</b></li>
            <li>Pilih menu <b>BRIVA</b></li>
            <li>Masukkan nomor BRIVA di atas</li>
            <li>Periksa nama & jumlah</li>
            <li>Klik <b>Bayar</b></li>
        </ul>
    </div>

    <p class="info">
        Setelah membayar, silakan upload bukti pembayaran<br>
        di halaman <b>Pemesanan Saya</b>.
    </p>

</div>

</body>
</html>