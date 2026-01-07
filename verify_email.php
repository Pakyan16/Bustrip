<?php
include 'koneksi.php';

$pesan = "Terjadi kesalahan. Email tidak dapat diverifikasi.";

if (isset($_GET['email'])) {
    $email = $_GET['email'];

    $update = mysqli_query($conn, "UPDATE user SET status='1' WHERE email='$email'");

    if ($update) {
        $pesan = "Email kamu berhasil diverifikasi! Akun kamu sekarang sudah aktif.";
    } else {
        $pesan = "Terjadi kesalahan saat memverifikasi email. Silakan coba lagi.";
    }
}
?>


<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Verifikasi Email - BusTrip</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      background: linear-gradient(to right, #ffb347, #ffcc33);
      font-family: 'Poppins', sans-serif;
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .logo-text {
      font-weight: 700;
      color: #006400;
      letter-spacing: 1px;
    }
    .success-icon {
      font-size: 70px;
      color: #28a745;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>

<div class="container vh-100 d-flex justify-content-center align-items-center">
  <div class="card p-5 text-center" style="width: 450px;">
    <h3 class="logo-text mb-3">🚌 BusTrip</h3>

    <?php if (strpos($pesan, 'berhasil') !== false): ?>
      <div class="success-icon">✅</div>
      <h4 class="text-success">Verifikasi Berhasil</h4>
      <p class="mt-3 text-dark"><?= $pesan; ?></p>
      <a href="login.php" class="btn btn-success mt-4 px-4">Ke Halaman Login</a>
    <?php else: ?>
      <div class="text-danger fs-1 mb-2">❌</div>
      <h4 class="text-danger">Verifikasi Gagal</h4>
      <p class="mt-3 text-dark"><?= $pesan; ?></p>
      <a href="register.php" class="btn btn-warning mt-4 px-4">Kembali ke Register</a>
    <?php endif; ?>
  </div>
</div>

</body>
</html>