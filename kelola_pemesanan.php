<?php
session_start();
include 'koneksi.php';

// Cek login admin
if (!isset($_SESSION['email']) || $_SESSION['role'] != 'ADMIN') {
    header("Location: login.php");
    exit;
}

// Hapus pemesanan
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $conn->query("DELETE FROM pemesanan_tiket WHERE pemesanan_id='$id'");
    echo "<script>alert('Pemesanan berhasil dihapus!');window.location='kelola_pemesanan.php';</script>";
}

// Update status pemesanan
if (isset($_POST['update_status'])) {
    $id = $_POST['pemesanan_id'];
    $status = $_POST['status_pesanan'];
    $conn->query("UPDATE pemesanan_tiket SET status_pesanan='$status' WHERE pemesanan_id='$id'");
    echo "<script>alert('Status pemesanan berhasil diperbarui!');window.location='kelola_pemesanan.php';</script>";
}

// Ambil semua data pemesanan
$query = "
SELECT 
    pt.pemesanan_id,
    u.nama AS nama_penumpang,
    CONCAT(jk.asal, ' - ', jk.tujuan) AS rute,
    jk.tanggal,
    jk.jam,
    pt.status_pesanan,
    COALESCE(py.status, 'BELUM BAYAR') AS status_pembayaran
FROM pemesanan_tiket pt
JOIN penumpang p ON pt.penumpang_id = p.penumpang_id
JOIN user u ON p.user_id = u.user_id
JOIN jadwal_keberangkatan jk ON pt.jadwal_id = jk.jadwal_id
LEFT JOIN pembayaran py ON py.pemesanan_id = pt.pemesanan_id
ORDER BY jk.tanggal DESC
";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Pemesanan | BusTrip Admin</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<style>
    body { background-color: #f7f7f7; font-family: 'Poppins', sans-serif; }

    /* Sidebar */
    .sidebar {
        width: 250px;
        background: #ff9800;
        position: fixed;
        top: 0; left: 0;
        height: 100vh;
        padding: 20px 0;
        color: white;
    }
    .sidebar a {
        display: block;
        padding: 12px 20px;
        text-decoration: none;
        color: white;
        font-size: 16px;
        margin-bottom: 5px;
    }
    .sidebar a:hover, .active {
        background: #e68900;
        border-radius: 8px;
    }

    /* Content */
    .content {
        margin-left: 270px;
        padding: 25px;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    table th {
        background-color: #ffe8bf;
    }
    .btn-hapus { background: #dc3545; color: white; border-radius: 6px; padding: 5px 10px; }
    .btn-update { background: #28a745; color: white; border: none; padding: 5px 8px; border-radius: 6px; }
</style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-center mb-4"><i class="bi bi-bus-front-fill"></i> BusTrip Admin</h4>

    <a href="dashboard_admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="kelola_penumpang.php"><i class="bi bi-people"></i> Kelola Penumpang</a>
    <a href="kelola_jadwal.php"><i class="bi bi-calendar-week"></i> Kelola Jadwal</a>
    <a href="kelola_pemesanan.php" class="active"><i class="bi bi-ticket-perforated"></i> Kelola Pemesanan</a>
    <a href="verifikasi_pembayaran.php"><i class="bi bi-cash-coin"></i> Verifikasi Pembayaran</a>
    <a href="logout.php" class="mt-4"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- Content -->
<div class="content">
    <div class="card p-4">
        <h3 class="mb-3"><i class="bi bi-ticket-detailed"></i> Kelola Pemesanan Tiket</h3>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Penumpang</th>
                        <th>Rute</th>
                        <th>Tanggal</th>
                        <th>Jam</th>
                        <th>Status Pesanan</th>
                        <th>Status Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php 
                if ($result->num_rows > 0): 
                    $no = 1;
                    while ($row = $result->fetch_assoc()): 
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($row['nama_penumpang']) ?></td>
                        <td><?= htmlspecialchars($row['rute']) ?></td>
                        <td><?= htmlspecialchars($row['tanggal']) ?></td>
                        <td><?= htmlspecialchars($row['jam']) ?></td>

                        <td>
                            <form method="POST" class="d-flex gap-1">
                                <input type="hidden" name="pemesanan_id" value="<?= $row['pemesanan_id'] ?>">
                                <select name="status_pesanan" class="form-select form-select-sm">
                                    <option value="PENDING" <?= $row['status_pesanan']=='PENDING'?'selected':'' ?>>PENDING</option>
                                    <option value="LUNAS" <?= $row['status_pesanan']=='LUNAS'?'selected':'' ?>>LUNAS</option>
                                    <option value="BATAL" <?= $row['status_pesanan']=='BATAL'?'selected':'' ?>>BATAL</option>
                                </select>
                                <button name="update_status" class="btn-update">Update</button>
                            </form>
                        </td>

                        <td><?= htmlspecialchars($row['status_pembayaran']) ?></td>

                        <td>
                            <a href="?hapus=<?= $row['pemesanan_id'] ?>"
                               onclick="return confirm('Yakin ingin menghapus?')"
                               class="btn-hapus">
                               <i class="bi bi-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                <?php endwhile; else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada data pemesanan</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>
</html>
