<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'PENUMPANG') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$penumpang = $conn->query("SELECT * FROM penumpang WHERE user_id = '$user_id'")->fetch_assoc();
$penumpang_id = $penumpang['penumpang_id'] ?? 0;


// Jika tombol pesan ditekan
if (isset($_POST['pesan'])) {
    $jadwal_id = $_POST['jadwal_id'];
    $kursi_id = $_POST['kursi_id']; // TAMBAH INI
    $tanggal_pesan = date('Y-m-d');
    $status_pesanan = 'Menunggu Pembayaran';

    // Insert ke pemesanan_tiket
    $conn->query("INSERT INTO pemesanan_tiket 
        (penumpang_id, jadwal_id, kursi_id, tanggal_pesan, status_pesanan)
        VALUES ('$penumpang_id', '$jadwal_id', '$kursi_id', '$tanggal_pesan', '$status_pesanan')");
    
    // Update status kursi menjadi BOOKED
    $conn->query("UPDATE kursi SET status='BOOKED' WHERE kursi_id='$kursi_id'");

    echo "<script>alert('Tiket berhasil dipesan!');window.location='dashboard_penumpang.php';</script>";
    exit;
}


// Ambil semua jadwal keberangkatan yang tersedia
$jadwal = $conn->query("
    SELECT jk.jadwal_id, b.nama_bus, jk.asal, jk.tujuan, jk.tanggal, jk.jam, jk.harga
    FROM jadwal_keberangkatan jk
    JOIN bus b ON jk.bus_id = b.bus_id
    ORDER BY jk.tanggal ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pesan Tiket</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(180deg, #ffa500, #ffcc33);
    min-height: 100vh;
    font-family: 'Poppins', sans-serif;
}
.navbar {
    background: linear-gradient(90deg, #ff8c00, #ffcc33);
}
.navbar-brand, .nav-link {
    color: white !important;
    font-weight: bold;
}
.card {
    border-radius: 15px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
.table thead {
    background-color: #fff3cd;
}
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="dashboard_penumpang.php">🚍 BusTrip</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="dashboard_penumpang.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link active" href="#">Pesan Tiket</a></li>
                <li class="nav-item"><a class="nav-link" href="profil.php">Profil</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-4">
    <h3 class="text-center fw-bold text-success">Pesan Tiket Bus</h3>

    <div class="card p-3 mt-3">
        <table class="table table-bordered mt-2">
            <thead class="text-center">
                <tr>
                    <th>Bus</th>
                    <th>Rute</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Harga</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $jadwal->fetch_assoc()): ?>
                <tr class="text-center">
                    <td><?= htmlspecialchars($row['nama_bus']) ?></td>
                    <td><?= htmlspecialchars($row['asal']) ?> - <?= htmlspecialchars($row['tujuan']) ?></td>
                    <td><?= htmlspecialchars(date('d M Y', strtotime($row['tanggal']))) ?></td>
                    <td><?= htmlspecialchars($row['jam']) ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td>
                        <a href="pilih_kursi.php?bus_id=<?= $row['bus_id'] ?>&jadwal_id=<?= $row['jadwal_id'] ?>" 
   class="btn btn-success btn-sm">
   Pesan
</a>

                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
