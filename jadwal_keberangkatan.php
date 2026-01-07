<?php
session_start();
include 'koneksi.php';

// Cek login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'PENUMPANG') {
  header("Location: login.php");
  exit;
}

// Ambil jadwal
// Query dasar
$query = "
  SELECT j.*, b.nama_po, b.kelas_bus 
  FROM jadwal_keberangkatan j
  JOIN bus b ON j.bus_id = b.bus_id
  WHERE 1=1
";

// Jika ada pencarian
if (!empty($_GET['q'])) {
  $q = $_GET['q'];
  $query .= "
    AND (
      b.nama_po LIKE '%$q%' OR
      b.kelas_bus LIKE '%$q%' OR
      j.asal LIKE '%$q%' OR
      j.tujuan LIKE '%$q%'
    )
  ";
}

// Urutkan
$query .= " ORDER BY j.tanggal ASC, j.jam ASC";

// Eksekusi
$jadwalQuery = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pesan Tiket | BusTrip</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  body { background: #f4f4f4; }
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
  .sidebar a:hover { background: rgba(255,255,255,0.2); }
  .content { margin-left: 270px; padding: 25px; }
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

  <h3 class="fw-bold mb-3"><i class="fa-solid fa-ticket me-2"></i>Pesan Tiket</h3>

  <form method="GET" class="mb-3">
  <div class="input-group">
    <input type="text" name="q" class="form-control" 
           placeholder="Cari jadwal (PO, asal, tujuan, kelas)..."
           value="<?= $_GET['q'] ?? '' ?>">
    <button class="btn btn-primary"><i class="fa-solid fa-search"></i> Cari</button>
    <a href="jadwal_keberangkatan.php" class="btn btn-secondary">Reset</a>
  </div>
</form>



  <div class="card p-3">
    <table class="table table-bordered text-center mt-2">
      <thead class="table-warning">
        <tr>
          <th>Nama PO</th>
          <th>Kelas</th>
          <th>Rute</th>
          <th>Tanggal</th>
          <th>Jam</th>
          <th>Harga</th>
          <th>Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($jadwalQuery->num_rows == 0): ?>
          <tr><td colspan="7" class="text-muted">Tidak ada jadwal</td></tr>
        <?php else: while ($j = $jadwalQuery->fetch_assoc()): ?>
        <tr>
          <td><?= $j['nama_po'] ?></td>
          <td><?= $j['kelas_bus'] ?></td>
          <td><?= $j['asal'] ?> → <?= $j['tujuan'] ?></td>
          <td><?= $j['tanggal'] ?></td>
          <td><?= $j['jam'] ?></td>
          <td>Rp <?= number_format($j['harga'], 0, ',', '.') ?></td>
          <td>
            <a href="pilih_kursi.php?bus_id=<?= $j['bus_id'] ?>&jadwal_id=<?= $j['jadwal_id'] ?>" 
   class="btn btn-success btn-sm">
   Pilih Kursi
</a>



          </td>
        </tr>
        <?php endwhile; endif; ?>
      </tbody>
    </table>
  </div>

</div>

</body>
</html>
