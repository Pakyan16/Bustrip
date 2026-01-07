<?php
session_start();
include 'koneksi.php';

// Hanya user yang login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data pemesanan user
$query = $conn->query("
    SELECT pt.*, 
           j.asal, j.tujuan, j.tanggal, j.jam, j.harga,
           pb.metode, pb.jumlah, pb.bukti, pb.qr_code,
           u.nama
    FROM pemesanan_tiket pt
    JOIN penumpang p ON pt.penumpang_id = p.penumpang_id
    JOIN user u ON p.user_id = u.user_id
    JOIN jadwal_keberangkatan j ON pt.jadwal_id = j.jadwal_id
    LEFT JOIN pembayaran pb ON pt.pemesanan_id = pb.pemesanan_id
    WHERE u.user_id = '$user_id'
    ORDER BY pt.pemesanan_id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Pemesanan Saya | BusTrip</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f5f5;
            font-family: Poppins, sans-serif;
            overflow-x: hidden;
        }
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #ff9d00;
            position: fixed;
            left: 0;
            top: 0;
            padding: 25px 20px;
            color: white;
        }
        .sidebar a {
            text-decoration: none;
            color: white;
            display: block;
            padding: 10px 12px;
            margin-bottom: 8px;
            border-radius: 10px;
            font-weight: 500;
        }
        .sidebar a:hover,
        .sidebar .active {
            background: rgba(255,255,255,0.25);
        }
        .content {
            margin-left: 270px;
            padding: 30px;
        }
        .card {
            border-radius: 15px;
        }
        .badge { font-size: 13px; }
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
    <h3 class="mb-4 fw-bold">📄 Daftar Pemesanan Tiket Anda</h3>

    <?php if (isset($_GET['status']) && $_GET['status'] == "sukses"): ?>
    <div class="alert alert-success">Bukti pembayaran berhasil dikirim! Menunggu verifikasi admin.</div>
    <?php endif; ?>

    <?php if ($query->num_rows == 0): ?>
        <div class="alert alert-secondary text-center">Anda belum memiliki pemesanan tiket.</div>
    <?php endif; ?>

    <?php while ($row = $query->fetch_assoc()): ?>
    <div class="card shadow p-3 mb-3">
        <div class="row">

            <!-- Info Tiket -->
            <div class="col-md-8">
                <h5 class="fw-bold"><?= $row['asal'] ?> → <?= $row['tujuan'] ?></h5>
                <p class="mb-1 text-muted">
                    🗓 <?= $row['tanggal'] ?> &nbsp; | &nbsp; 🕒 <?= $row['jam'] ?>
                </p>
                <p class="mb-1 fw-bold text-success">
                    💰 Harga: Rp <?= number_format($row['harga'], 0, ',', '.') ?>
                </p>

                <!-- Status -->
                <p class="mt-2">
                    Status Pembayaran: 
                    <?php if ($row['status_pembayaran'] == 'BELUM BAYAR'): ?>
                        <span class="badge bg-danger">BELUM BAYAR</span>

                    <?php elseif ($row['status_pembayaran'] == 'MENUNGGU VERIFIKASI'): ?>
                        <span class="badge bg-warning text-dark">MENUNGGU VERIFIKASI</span>

                    <?php elseif ($row['status_pembayaran'] == 'LUNAS'): ?>
                        <span class="badge bg-success">LUNAS</span>

                    <?php else: ?>
                        <span class="badge bg-secondary"><?= $row['status_pembayaran'] ?></span>
                    <?php endif; ?>
                </p>

                <!-- QR CODE -->
                <?php if ($row['status_pembayaran'] == "LUNAS"): ?>
                    <p class="text-success fw-semibold mt-2">✔ Pembayaran sudah lunas.</p>
                <?php else: ?>
                    <?php if (!empty($row['qr_code'])): ?>
                        <p class="mt-2 mb-1 fw-semibold">🔳 QR Pembayaran:</p>
                        <img src="<?= $row['qr_code'] ?>" width="150" class="border rounded">
                    <?php else: ?>
                        <a href="generate_qr.php?pemesanan_id=<?= $row['pemesanan_id'] ?>" 
                        class="btn btn-success btn-sm mt-2">
                            🔳 Generate QR Pembayaran
                        </a>
                    <?php endif; ?>
                <?php endif; ?>

            </div>

            <!-- Aksi -->
            <div class="col-md-4 text-end">
                <?php if ($row['status_pembayaran'] == "LUNAS"): ?>
                    <a href="tiket_saya.php?pemesanan_id=<?= $row['pemesanan_id'] ?>" 
                    class="btn btn-success btn-sm">🎟 Lihat Tiket</a>

                <?php else: ?>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalUpload<?= $row['pemesanan_id'] ?>">
                        📤 Upload Pembayaran
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- MODAL UPLOAD -->
    <div class="modal fade" id="modalUpload<?= $row['pemesanan_id'] ?>" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">

          <form action="upload_pembayaran.php" method="POST" enctype="multipart/form-data">
            
            <div class="modal-header">
              <h5 class="modal-title">Upload Bukti Pembayaran</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
              <input type="hidden" name="pemesanan_id" value="<?= $row['pemesanan_id'] ?>">

              <label class="fw-semibold">Metode Pembayaran</label>
              <select name="metode" class="form-select mb-3" required>
                <option value="TRANSFER">Transfer Bank</option>
                <option value="DANA">Dana</option>
                <option value="OVO">Ovo</option>
                <option value="GOPAY">Gopay</option>
              </select>

              <label class="fw-semibold">Jumlah Pembayaran</label>
              <input type="number" name="jumlah" class="form-control mb-3" required>

              <label class="fw-semibold">Upload Bukti</label>
              <input type="file" name="bukti" class="form-control" required>
            </div>

            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Upload</button>
            </div>

          </form>

        </div>
      </div>
    </div>

    <?php endwhile; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
