<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: login.php");
    exit;
}

// Hitung total penumpang
$total_penumpang = $conn->query("SELECT COUNT(*) AS total FROM penumpang")->fetch_assoc()['total'] ?? 0;

// Ambil data penumpang
$data_penumpang = $conn->query("
    SELECT p.penumpang_id, u.nama, u.email, p.no_hp
    FROM penumpang p
    JOIN user u ON p.user_id = u.user_id
    ORDER BY p.penumpang_id DESC
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Penumpang - BusTrip Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body {
        background: #fdf7e8;
        font-family: 'Poppins', sans-serif;
    }

    /* Sidebar */
    .sidebar {
        position: fixed;
        top: 0; left: 0;
        height: 100vh;
        width: 240px;
        background: #ff9900;
        padding-top: 20px;
        color: white;
    }
    .sidebar .brand {
        font-size: 22px;
        font-weight: bold;
        padding: 15px 20px;
    }
    .sidebar a {
        display: block;
        padding: 12px 20px;
        color: white;
        text-decoration: none;
        font-size: 15px;
        transition: 0.2s;
    }
    .sidebar a:hover {
        background: rgba(255,255,255,0.2);
    }
    .sidebar a.active {
        background: white;
        color: #ff9900;
        font-weight: 600;
    }

    /* Content */
    .content {
        margin-left: 250px;
        padding: 25px;
    }

    /* Card statistik */
    .card-stat {
        border-radius: 15px;
        padding: 20px;
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        text-align: center;
    }
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="brand">🚌 BusTrip Admin</div>

    <a href="dashboard_admin.php">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>

    <a href="kelola_penumpang.php" class="active">
        <i class="bi bi-people me-2"></i> Kelola Penumpang
    </a>

    <a href="kelola_jadwal.php">
        <i class="bi bi-calendar2-check me-2"></i> Kelola Jadwal
    </a>

    <a href="kelola_pemesanan.php">
        <i class="bi bi-ticket-perforated me-2"></i> Kelola Pemesanan
    </a>

    <a href="verifikasi_pembayaran.php">
        <i class="bi bi-cash-stack me-2"></i> Verifikasi Pembayaran
    </a>

    <a href="logout.php" class="mt-3">
        <i class="bi bi-box-arrow-right me-2"></i> Logout
    </a>
</div>

<!-- CONTENT -->
<div class="content">

    <h3 class="fw-bold text-success">Kelola Penumpang</h3>
    <p>Manajemen data penumpang BusTrip.</p>

    <!-- CARD STAT -->
    <div class="row g-3 mt-2">
        <div class="col-md-3">
            <div class="card-stat">
                👤 <br>
                <h4><?= $total_penumpang ?></h4>
                <small>Total Penumpang</small>
            </div>
        </div>
    </div>

    <!-- TABEL -->
    <div class="card p-4 mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">📋 Data Penumpang</h5>
            <a href="tambah_penumpang.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Tambah Penumpang
            </a>
        </div>

        <table class="table table-bordered table-striped">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>No HP</th>
                    <th>Email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($p = $data_penumpang->fetch_assoc()): ?>
                <tr>
    <td><?= $p['penumpang_id'] ?></td>
    <td><?= $p['nama'] ?></td>
    <td><?= $p['no_hp'] ?></td>
    <td><?= $p['email'] ?></td>
    <td>
        <a href="edit_penumpang.php?id=<?= $p['penumpang_id'] ?>" class="btn btn-warning btn-sm">
            <i class="bi bi-pencil"></i>
        </a>
        <a onclick="return confirm('Hapus penumpang ini?')" 
           href="hapus_penumpang.php?id=<?= $p['penumpang_id'] ?>" 
           class="btn btn-danger btn-sm">
            <i class="bi bi-trash"></i>
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
