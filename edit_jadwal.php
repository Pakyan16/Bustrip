<?php
session_start();
include 'koneksi.php';

// Cek login & role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: login.php");
    exit;
}

// Pastikan ada ID jadwal
if (!isset($_GET['id'])) {
    echo "<script>alert('ID jadwal tidak ditemukan!'); window.location='kelola_jadwal.php';</script>";
    exit;
}

$id = intval($_GET['id']);

// Ambil data jadwal
$result = mysqli_query($conn, "SELECT * FROM jadwal_keberangkatan WHERE jadwal_id='$id'");
$jadwal = mysqli_fetch_assoc($result);

if (!$jadwal) {
    echo "<script>alert('Data jadwal tidak ditemukan!'); window.location='kelola_jadwal.php';</script>";
    exit;
}

// Ambil data bus
$busData = mysqli_query($conn, "SELECT * FROM bus");

// Update jadwal
if (isset($_POST['update'])) {
    $bus_id  = $_POST['bus_id'];
    $asal    = $_POST['asal'];
    $tujuan  = $_POST['tujuan'];
    $tanggal = $_POST['tanggal'];
    $jam     = $_POST['jam'];
    $harga   = $_POST['harga'];

    $query = "UPDATE jadwal_keberangkatan SET 
                bus_id='$bus_id',
                asal='$asal',
                tujuan='$tujuan',
                tanggal='$tanggal',
                jam='$jam',
                harga='$harga'
              WHERE jadwal_id='$id'";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Jadwal berhasil diperbarui!'); window.location='kelola_jadwal.php';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui jadwal');</script>";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Edit Jadwal Keberangkatan</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background-color: #f7f7f7;
        font-family: 'Poppins', sans-serif;
    }
    .sidebar {
        width: 250px;
        background: #ff9800;
        min-height: 100vh;
        position: fixed;
        padding-top: 20px;
    }
    .sidebar a {
        display: block;
        padding: 12px 20px;
        color: white;
        text-decoration: none;
        font-size: 16px;
        margin-bottom: 5px;
    }
    .sidebar a:hover, .active {
        background: #e68900;
        border-radius: 8px;
    }
    .content {
        margin-left: 270px;
        padding: 25px;
    }
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .btn-primary {
        background-color: #ff9800;
        border: none;
    }
    .btn-primary:hover {
        background-color: #e68900;
    }
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4 class="text-white text-center mb-4">🚌 BusTrip Admin</h4>

    <a href="dashboard_admin.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="kelola_penumpang.php"><i class="bi bi-people"></i> Kelola Penumpang</a>
    <a href="kelola_jadwal.php" class="active"><i class="bi bi-calendar-week"></i> Kelola Jadwal</a>
    <a href="kelola_pemesanan.php"><i class="bi bi-ticket-perforated"></i> Kelola Pemesanan</a>
    <a href="verifikasi_pembayaran.php"><i class="bi bi-cash-coin"></i> Verifikasi Pembayaran</a>
    <a href="logout.php" class="mt-5"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>

<!-- Content -->
<div class="content">
    <div class="card p-4">
        <h3 class="mb-3 text-center">✏️ Edit Jadwal Keberangkatan</h3>

        <form method="POST">
            <div class="row g-3">

                <div class="col-md-4">
                    <label>Bus</label>
                    <select name="bus_id" class="form-select" required>
                        <option value="">Pilih Bus</option>
                        <?php while ($bus = mysqli_fetch_assoc($busData)): ?>
                            <option value="<?= $bus['bus_id'] ?>" <?= ($bus['bus_id'] == $jadwal['bus_id']) ? 'selected' : '' ?>>
                                <?= $bus['nama_po'] ?> (<?= $bus['kelas_bus'] ?>)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Asal</label>
                    <input type="text" name="asal" class="form-control" value="<?= $jadwal['asal'] ?>" required>
                </div>

                <div class="col-md-4">
                    <label>Tujuan</label>
                    <input type="text" name="tujuan" class="form-control" value="<?= $jadwal['tujuan'] ?>" required>
                </div>

                <div class="col-md-4">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= $jadwal['tanggal'] ?>" required>
                </div>

                <div class="col-md-4">
                    <label>Jam</label>
                    <input type="time" name="jam" class="form-control" value="<?= $jadwal['jam'] ?>" required>
                </div>

                <div class="col-md-4">
                    <label>Harga</label>
                    <input type="number" name="harga" class="form-control" value="<?= $jadwal['harga'] ?>" required>
                </div>

                <div class="col-12 mt-3">
                    <button type="submit" name="update" class="btn btn-primary w-100">💾 Simpan Perubahan</button>
                </div>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="kelola_jadwal.php" class="btn btn-secondary">⬅ Kembali</a>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</body>
</html>
