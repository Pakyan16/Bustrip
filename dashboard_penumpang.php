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

// Ambil semua jadwal keberangkatan
$jadwalDashboard = mysqli_query($conn, "
  SELECT j.*, b.nama_po, b.kelas_bus
  FROM jadwal_keberangkatan j
  JOIN bus b ON j.bus_id = b.bus_id
  ORDER BY j.tanggal ASC, j.jam ASC
");

if (!$jadwalDashboard) {
    die("Query Error: " . mysqli_error($conn));
}


// Ambil perjalanan selanjutnya
$tiketAktif = mysqli_query($conn, "
  SELECT pt.*, j.* FROM pemesanan_tiket pt
  JOIN penumpang p ON pt.penumpang_id=p.penumpang_id
  JOIN jadwal_keberangkatan j ON pt.jadwal_id=j.jadwal_id
  WHERE p.user_id='{$user['user_id']}' AND j.tanggal >= CURDATE()
  ORDER BY j.tanggal ASC
  LIMIT 1
");
$nextTrip = mysqli_fetch_assoc($tiketAktif);

// Statistik
$totalTiket = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT COUNT(*) as jml 
  FROM pemesanan_tiket pt 
  JOIN penumpang p ON pt.penumpang_id=p.penumpang_id 
  WHERE p.user_id='{$user['user_id']}'"))['jml'];

$tiketAktifCount = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT COUNT(*) as jml 
  FROM pemesanan_tiket pt 
  JOIN penumpang p ON pt.penumpang_id=p.penumpang_id 
  JOIN jadwal_keberangkatan j ON pt.jadwal_id=j.jadwal_id 
  WHERE p.user_id='{$user['user_id']}' AND j.tanggal >= CURDATE()"))['jml'];

$tiketSelesai = mysqli_fetch_assoc(mysqli_query($conn, "
  SELECT COUNT(*) as jml 
  FROM pemesanan_tiket pt 
  JOIN penumpang p ON pt.penumpang_id=p.penumpang_id 
  JOIN jadwal_keberangkatan j ON pt.jadwal_id=j.jadwal_id 
  WHERE p.user_id='{$user['user_id']}' AND j.tanggal < CURDATE()"))['jml'];

// Promo dummy
$promo = [
  ["kode" => "HEMAT10", "desc" => "Diskon 10% untuk semua rute"],
  ["kode" => "AKHIRTAHUN", "desc" => "Promo akhir tahun 20%"],
];
$jadwal_info = $conn->query("
    SELECT 
        jk.jadwal_id,
        bus.nama_po AS bus,
        bus.kelas_bus,
        CONCAT(jk.asal, ' - ', jk.tujuan) AS rute,
        jk.tanggal,
        jk.jam,
        jk.harga,
        jk.status
    FROM jadwal_keberangkatan jk
    JOIN bus ON bus.bus_id = jk.bus_id
    ORDER BY jk.tanggal ASC, jk.jam ASC
");


?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Dashboard Penumpang | BusTrip</title>
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

  .sidebar h3 {
    font-weight: 700;
  }

  .sidebar a {
    display: block;
    padding: 12px;
    color: white;
    text-decoration: none;
    margin-bottom: 10px;
    border-radius: 8px;
    font-size: 15px;
  }

  .sidebar a:hover {
    background: rgba(255,255,255,0.2);
  }

  .sidebar i {
    width: 22px;
  }

  /* CONTENT */
  .content {
    margin-left: 270px;
    padding: 25px;
  }

  .stat-card {
    border-radius: 15px;
    padding: 20px;
    color: white;
  }

  .promo-card {
    border-left: 5px solid #ff9800;
    padding: 10px 15px;
    background: white;
    border-radius: 10px;
  }
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
  <h3><i class="fa-solid fa-bus me-2"></i>BusTrip</h3>
  <hr class="border-light">

  <a href="dashboard_penumpang.php"><i class="fa-solid fa-house"></i> Dashboard</a>
  <a href="jadwal_keberangkatan.php"><i class="fa-solid fa-ticket"></i> Pesan Tiket</a>
  <a href="pemesanan_saya.php"><i class="fa-solid fa-list"></i> Pemesanan Saya</a>
  <a href="#"><i class="fa-solid fa-gift"></i> Promo</a>
  <a href="profil_penumpang.php"><i class="fa-solid fa-user"></i> Profil</a>
  <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
</div>

<!-- CONTENT -->
<div class="content">

  <h3 class="fw-bold mb-3">Dashboard Penumpang</h3>

  <!-- Statistik -->
  <div class="row mb-4">
    <div class="col-md-4">
      <div class="stat-card bg-primary text-center">
        <h4><i class="fa-solid fa-ticket me-1"></i><?= $totalTiket ?></h4>
        <p>Total Tiket</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card bg-success text-center">
        <h4><i class="fa-solid fa-clock me-1"></i><?= $tiketAktifCount ?></h4>
        <p>Tiket Aktif</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card bg-secondary text-center">
        <h4><i class="fa-solid fa-check me-1"></i><?= $tiketSelesai ?></h4>
        <p>Tiket Selesai</p>
      </div>
    </div>
  </div>


  <!-- Promo -->
  <div class="card p-3 mb-4">
    <h5 class="fw-bold"><i class="fa-solid fa-gift me-2"></i>Promo & Kupon</h5>
    <?php foreach ($promo as $p): ?>
      <div class="promo-card mb-2">
        <strong><?= $p['kode'] ?></strong><br>
        <small><?= $p['desc'] ?></small>
      </div>
    <?php endforeach; ?>
  </div>

    <!-- Jadwal Keberangkatan (Hanya Info) -->
<div class="card p-3 mt-4">
    <h5>🚌 Jadwal Keberangkatan</h5>
    <p class="text-muted">Informasi jadwal bus. Anda tidak bisa memesan dari sini.</p>

    <table class="table table-bordered mt-3">
        <thead class="table-light">
            <tr>
                <th>Bus</th>
                <th>Rute</th>
                <th>Tanggal</th>
                <th>Waktu</th>
                <th>Harga</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($jadwal_info->num_rows > 0): ?>
                <?php while ($row = $jadwal_info->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['bus'] ?> (<?= $row['kelas_bus'] ?>)</td>
                    <td><?= $row['rute'] ?></td>
                    <td><?= $row['tanggal'] ?></td>
                    <td><?= $row['jam'] ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>

                    <td>
                        <?php if ($row['status'] == "Menunggu"): ?>
                            <span class="badge bg-warning text-dark">Menunggu</span>
                        <?php else: ?>
                            <span class="badge bg-success">Berangkat</span>
                        <?php endif; ?>
                    </td>

                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="text-center text-muted">Belum ada jadwal keberangkatan</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
 

</div>
</body>
</html>
