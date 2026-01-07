<?php
session_start();
include 'koneksi.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: login.php");
    exit;
}

$total_penumpang = $conn->query("SELECT COUNT(*) as total FROM penumpang")->fetch_assoc()['total'] ?? 0;
$total_jadwal = $conn->query("SELECT COUNT(*) as total FROM jadwal_keberangkatan")->fetch_assoc()['total'] ?? 0;
$total_pemesanan = $conn->query("SELECT COUNT(*) as total FROM pemesanan_tiket")->fetch_assoc()['total'] ?? 0;
$total_pendapatan = $conn->query("SELECT IFNULL(SUM(jumlah),0) as total FROM pembayaran")->fetch_assoc()['total'] ?? 0;

$aktivitas = [];
$result = $conn->query("
    (SELECT 'Penumpang baru' AS jenis, p.penumpang_id AS keterangan, NOW() AS tanggal 
     FROM penumpang p ORDER BY p.penumpang_id DESC LIMIT 5)
    UNION
    (SELECT 'Pemesanan baru', pt.pemesanan_id, pt.tanggal_pesan 
     FROM pemesanan_tiket pt ORDER BY pt.tanggal_pesan DESC LIMIT 5)
    UNION
    (SELECT 'Jadwal baru', CONCAT(j.asal,' - ', j.tujuan), j.tanggal 
     FROM jadwal_keberangkatan j ORDER BY j.tanggal DESC LIMIT 5)
    ORDER BY tanggal DESC LIMIT 5
");

while ($row = $result->fetch_assoc()) {
    $aktivitas[] = $row;
}

$jadwal_hari_ini = $conn->query("
    SELECT 
        jk.jadwal_id,
        bus.nama_po AS bus,
        CONCAT(jk.asal, ' - ', jk.tujuan) AS rute,
        jk.tanggal AS tanggal,
        jk.jam AS waktu,
        jk.harga,
        jk.status
    FROM jadwal_keberangkatan jk
    JOIN bus ON bus.bus_id = jk.bus_id
    ORDER BY jk.tanggal ASC
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - BusTrip</title>

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

        /* Cards */
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

    <a href="dashboard_admin.php" class="active">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard
    </a>

    <a href="kelola_penumpang.php">
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
    <?php if (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
    <div class="alert alert-success">Status berhasil diubah menjadi "Berangkat"</div>
<?php endif; ?>

    <h3 class="fw-bold text-success">Dashboard Admin</h3>
    <p>Selamat datang, <strong><?= $_SESSION['email'] ?></strong></p>

    <div class="row g-3 mt-3">

        <div class="col-md-3">
            <div class="card-stat">
                👤 <br>
                <h4><?= $total_penumpang ?></h4>
                <small>Total Penumpang</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-stat">
                🚌 <br>
                <h4><?= $total_jadwal ?></h4>
                <small>Total Jadwal</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-stat">
                🎟️ <br>
                <h4><?= $total_pemesanan ?></h4>
                <small>Total Pemesanan</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card-stat">
                💰 <br>
                <h5>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h5>
                <small>Total Pendapatan</small>
            </div>
        </div>

    </div>

    <!-- Jadwal Hari Ini -->
    <div class="card p-3 mt-5">
        <h5>🗓️ Jadwal Keberangkatan Hari Ini</h5>
        <table class="table table-bordered mt-3">
            <thead class="table-light">
                <tr>
                    <th>Bus</th>
                    <th>Rute</th>
                    <th>Waktu</th>
                    <th>Harga</th>
                    <th>Status</th>
                    <th>Aksi</th>

                </tr>
            </thead>
            <tbody>
            <?php if ($jadwal_hari_ini->num_rows > 0): ?>
    <?php while ($row = $jadwal_hari_ini->fetch_assoc()): ?>
    <tr>
        <td><?= $row['bus'] ?></td>
        <td><?= $row['rute'] ?></td>
        <td><?= $row['waktu'] ?></td>
        <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>

        <td>
            <?php if ($row['status'] === "Menunggu"): ?>
                <span class="badge bg-warning text-dark">Menunggu</span>
            <?php else: ?>
                <span class="badge bg-success">Berangkat</span>
            <?php endif; ?>
        </td>

        <td>
            <?php if ($row['status'] === "Menunggu"): ?>
                <a href="ubah_status_jadwal.php?id=<?= $row['jadwal_id'] ?>"
                   class="btn btn-sm btn-primary">
                    Jadikan Berangkat
                </a>
            <?php else: ?>
                <button class="btn btn-sm btn-secondary" disabled>
                    Sudah Berangkat
                </button>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>

                <tr><td colspan="4" class="text-center text-muted">Tidak ada jadwal hari ini</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>
