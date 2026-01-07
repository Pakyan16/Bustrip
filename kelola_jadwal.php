<?php
session_start();
include 'koneksi.php';

// Cek role admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: login.php");
    exit;
}

// Proses tambah jadwal
if (isset($_POST['tambah'])) {
    $bus_id  = mysqli_real_escape_string($conn, $_POST['bus_id']);
    $asal    = mysqli_real_escape_string($conn, $_POST['asal']);
    $tujuan  = mysqli_real_escape_string($conn, $_POST['tujuan']);
    $tanggal = mysqli_real_escape_string($conn, $_POST['tanggal']);
    $jam     = mysqli_real_escape_string($conn, $_POST['jam']);
    $harga   = mysqli_real_escape_string($conn, $_POST['harga']);

    $query = "INSERT INTO jadwal_keberangkatan (bus_id, asal, tujuan, tanggal, jam, harga)
              VALUES ('$bus_id', '$asal', '$tujuan', '$tanggal', '$jam', '$harga')";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "<script>alert('Jadwal berhasil ditambahkan!'); window.location='kelola_jadwal.php';</script>";
    } else {
        echo "<script>alert('Gagal menambahkan jadwal: " . mysqli_error($conn) . "');</script>";
    }
}

// Hapus jadwal
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    mysqli_query($conn, "DELETE FROM jadwal_keberangkatan WHERE jadwal_id=$id");
    echo "<script>alert('Jadwal berhasil dihapus!'); window.location='kelola_jadwal.php';</script>";
    exit;
}

// Ambil data
$busData = mysqli_query($conn, "SELECT * FROM bus");
$jadwalData = mysqli_query($conn, "
    SELECT j.*, b.nama_po, b.kelas_bus
    FROM jadwal_keberangkatan j
    JOIN bus b ON j.bus_id = b.bus_id
    ORDER BY j.tanggal DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Jadwal - BusTrip Admin</title>

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

    <a href="kelola_jadwal.php" class="active">
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

    <h3 class="fw-bold text-success">Kelola Jadwal Keberangkatan</h3>
    <p>Manajemen data jadwal perjalanan BusTrip.</p>

    <!-- Form Tambah Jadwal -->
    <div class="card card-custom p-4 mb-4">
        <h5 class="mb-3">➕ Tambah Jadwal Baru</h5>

        <form method="POST">
            <div class="row g-3">
                
                <div class="col-md-4">
                    <label>Bus</label>
                    <select name="bus_id" class="form-select" required>
                        <option value="">Pilih Bus</option>
                        <?php while ($bus = mysqli_fetch_assoc($busData)): ?>
                        <option value="<?= $bus['bus_id'] ?>">
                            <?= $bus['nama_po'] ?> (<?= $bus['kelas_bus'] ?>)
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label>Asal</label>
                    <input type="text" name="asal" class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label>Tujuan</label>
                    <input type="text" name="tujuan" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label>Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label>Jam</label>
                    <input type="time" name="jam" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label>Harga</label>
                    <input type="number" name="harga" class="form-control" required>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" name="tambah" class="btn btn-success w-100">
                        Tambah
                    </button>
                </div>

            </div>
        </form>
    </div>

    <!-- Tabel Jadwal -->
    <div class="card card-custom p-4">
        <h5 class="mb-3">📅 Daftar Jadwal Keberangkatan</h5>

        <table class="table table-bordered table-striped align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Bus</th>
                    <th>Asal</th>
                    <th>Tujuan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Harga</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>

            <?php $no = 1; while ($row = mysqli_fetch_assoc($jadwalData)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['nama_po'] ?> (<?= $row['kelas_bus'] ?>)</td>
                    <td><?= $row['asal'] ?></td>
                    <td><?= $row['tujuan'] ?></td>
                    <td><?= $row['tanggal'] ?></td>
                    <td><?= $row['jam'] ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td class="text-center">
                        <a href="edit_jadwal.php?id=<?= $row['jadwal_id'] ?>" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="?hapus=<?= $row['jadwal_id'] ?>" class="btn btn-danger btn-sm"
                           onclick="return confirm('Yakin ingin menghapus jadwal ini?')">
                            <i class="bi bi-trash"></i> Hapus
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
