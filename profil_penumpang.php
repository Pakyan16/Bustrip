<?php
session_start();
include 'koneksi.php';

// Cek login & role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'PENUMPANG') {
  header("Location: login.php");
  exit;
}

// Ambil data user
$email = $_SESSION['email'];
$userQuery = mysqli_query($conn, "SELECT * FROM user WHERE email='$email'");
$user = mysqli_fetch_assoc($userQuery);

// Ambil profil penumpang
$penumpangQuery = mysqli_query($conn, "SELECT * FROM penumpang WHERE user_id='{$user['user_id']}'");
$penumpang = mysqli_fetch_assoc($penumpangQuery);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil Penumpang | BusTrip</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  body {
    font-family: 'Poppins', sans-serif;
    background: #f4f4f4;
  }

  /* SIDEBAR */
  .sidebar {
    width: 250px;
    height: 100vh;
    background: #ff9800;
    padding: 20px;
    position: fixed;
    color: white;
  }

  .sidebar a {
    display: block;
    padding: 12px;
    color: white;
    text-decoration: none;
    margin-bottom: 10px;
    border-radius: 8px;
  }
  .sidebar a:hover {
    background: rgba(255,255,255,0.2);
  }

  .sidebar h3 {
    font-weight: 700;
  }

  /* CONTENT */
  .content {
    margin-left: 270px;
    padding: 25px;
  }

  .profile-card {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
  }

  .profile-photo {
    width: 120px;
    height: 120px;
    background: #ddd;
    border-radius: 50%;
    margin: auto;
    display: block;
    font-size: 50px;
    color: #888;
    line-height: 120px;
    text-align: center;
  }

</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
  <h3><i class="fa-solid fa-bus me-2"></i>BusTrip</h3>
  <hr class="border-light">

  <a href="dashboard_penumpang.php"><i class="fa-solid fa-house"></i> Dashboard</a>
  <a href="pemesanan_saya.php"><i class="fa-solid fa-ticket"></i> Pemesanan Saya</a>
  <a href="#"><i class="fa-solid fa-gift"></i> Promo</a>
  <a href="profil_penumpang.php"><i class="fa-solid fa-user"></i> Profil</a>
  <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<!-- CONTENT -->
<div class="content">

  <h3 class="fw-bold mb-4">Profil Penumpang</h3>

  <div class="profile-card">

    <div class="text-center mb-3">
      <div class="profile-photo">
        <i class="fa-solid fa-user"></i>
      </div>
      <h4 class="mt-3"><?= $user['nama']; ?></h4>
      <p class="text-muted"><?= $user['email']; ?></p>
    </div>

    <hr>

    <h5 class="fw-bold"><i class="fa-solid fa-id-card me-2"></i>Informasi Akun</h5>
    <table class="table">
      <tr>
        <th style="width:200px">Nama</th>
        <td><?= $user['nama']; ?></td>
      </tr>
      <tr>
        <th>Email</th>
        <td><?= $user['email']; ?></td>
      </tr>
    </table>

    <h5 class="fw-bold mt-4"><i class="fa-solid fa-user-tag me-2"></i>Informasi Penumpang</h5>
    <table class="table">
      <tr>
        <th style="width:200px">Nomor HP</th>
        <td><?= $penumpang['no_hp'] ?? '-' ?></td>
      </tr>
      <tr>
        <th>Alamat</th>
        <td><?= $penumpang['alamat'] ?? '-' ?></td>
      </tr>
    </table>

    <div class="mt-4 text-end">
      <a href="edit_profil.php" class="btn btn-warning text-white">
        <i class="fa-solid fa-pen-to-square me-1"></i> Edit Profil
      </a>
    </div>

  </div>

</div>

</body>
</html>
