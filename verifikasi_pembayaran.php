<?php
session_start();
include 'koneksi.php';

// Cek role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: login.php");
    exit;
}

// QUERY PEMBAYARAN YANG BELUM LUNAS
$query = $conn->query("
    SELECT pt.*, u.nama AS nama_penumpang, 
           j.asal, j.tujuan, j.tanggal, j.jam,
           pb.metode, pb.jumlah, pb.bukti
    FROM pemesanan_tiket pt
    JOIN penumpang p ON pt.penumpang_id = p.penumpang_id
    JOIN user u ON p.user_id = u.user_id
    JOIN jadwal_keberangkatan j ON pt.jadwal_id = j.jadwal_id
    LEFT JOIN pembayaran pb ON pt.pemesanan_id = pb.pemesanan_id
    WHERE pt.status_pembayaran != 'LUNAS'
    ORDER BY pt.pemesanan_id DESC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Verifikasi Pembayaran - BusTrip Admin</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
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

    .card-custom {
        border-radius: 15px;
        border: none;
        background: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    table thead {
        background: #ffe08a !important;
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

    <a href="kelola_penumpang.php">
        <i class="bi bi-people me-2"></i> Kelola Penumpang
    </a>

    <a href="kelola_jadwal.php">
        <i class="bi bi-calendar2-check me-2"></i> Kelola Jadwal
    </a>

    <a href="kelola_pemesanan.php">
        <i class="bi bi-ticket-perforated me-2"></i> Kelola Pemesanan
    </a>

    <a href="verifikasi_pembayaran.php" class="active">
        <i class="bi bi-cash-stack me-2"></i> Verifikasi Pembayaran
    </a>

    <a href="logout.php" class="mt-3">
        <i class="bi bi-box-arrow-right me-2"></i> Logout
    </a>
</div>

<!-- CONTENT -->
<div class="content">

    <h3 class="fw-bold text-success">Verifikasi Pembayaran Tiket</h3>
    <p>Daftar pembayaran yang menunggu persetujuan admin.</p>

    <div class="card card-custom p-4">
        <h5 class="mb-3">📄 Daftar Pembayaran Belum Lunas</h5>

        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Rute</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Metode</th>
                    <th>Jumlah</th>
                    <th>Bukti</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>

<?php 
if ($query->num_rows == 0): ?>
    <tr>
        <td colspan="10" class="text-center text-muted">Tidak ada pembayaran menunggu verifikasi.</td>
    </tr>

<?php 
else:
$no = 1;
while ($row = $query->fetch_assoc()): ?>
    <tr>
        <td><?= $no++ ?></td>
        <td><?= htmlspecialchars($row['nama_penumpang']) ?></td>
        <td><?= $row['asal'] . " → " . $row['tujuan'] ?></td>
        <td><?= $row['tanggal'] ?></td>
        <td><?= $row['jam'] ?></td>

        <td><?= $row['metode'] ?: '<span class="text-danger">-</span>' ?></td>

        <td>
            <?= $row['jumlah'] 
                ? "Rp " . number_format($row['jumlah'], 0, ',', '.')
                : '<span class="text-danger">-</span>' ?>
        </td>

        <td>
            <?php if ($row['bukti']): ?>
                <a href="uploads/<?= $row['bukti'] ?>" target="_blank">
                    <img src="uploads/<?= $row['bukti'] ?>" width="80" class="rounded shadow">
                </a>
            <?php else: ?>
                <span class="text-danger">Belum Upload</span>
            <?php endif; ?>
        </td>

        <td>
            <?php if ($row['status_pembayaran'] == "MENUNGGU VERIFIKASI"): ?>
                <span class="badge bg-warning text-dark">Menunggu</span>
            <?php else: ?>
                <span class="badge bg-secondary"><?= $row['status_pembayaran'] ?></span>
            <?php endif; ?>
        </td>

        <td class="text-center">
            <div class="d-flex gap-2 justify-content-center">

                <form action="proses_verifikasi.php" method="POST">
                    <input type="hidden" name="pemesanan_id" value="<?= $row['pemesanan_id'] ?>">
                    <input type="hidden" name="aksi" value="SETUJU">
                    <button class="btn btn-success btn-sm">
                        <i class="bi bi-check2-circle"></i>
                    </button>
                </form>

                <form action="proses_verifikasi.php" method="POST">
                    <input type="hidden" name="pemesanan_id" value="<?= $row['pemesanan_id'] ?>">
                    <input type="hidden" name="aksi" value="TOLAK">
                    <button class="btn btn-danger btn-sm">
                        <i class="bi bi-x-circle"></i>
                    </button>
                </form>

            </div>
        </td>

    </tr>
<?php endwhile; endif; ?>

            </tbody>
        </table>
    </div>

</div>

</body>
</html>
