<?php
session_start();
include 'koneksi.php'; // sesuaikan path

// Cek apakah admin
// if($_SESSION['role'] != "ADMIN"){ die("Akses ditolak!"); }

$data = $conn->query("
    SELECT bayar.*, 
           p.penumpang_id, p.jadwal_id,
           j.asal, j.tujuan, j.tanggal, j.jam,
           pn.user_id,
           u.nama, u.email
    FROM pembayaran bayar
    JOIN pemesanan_tiket p ON bayar.pemesanan_id = p.pemesanan_id
    JOIN jadwal_keberangkatan j ON p.jadwal_id = j.jadwal_id
    JOIN penumpang pn ON p.penumpang_id = pn.penumpang_id
    JOIN user u ON pn.user_id = u.user_id
    ORDER BY bayar.pembayaran_id DESC
");
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background:#ffe082">

<nav class="navbar navbar-dark" style="background:#ffb300;">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">Admin | Verifikasi Pembayaran</span>
    </div>
</nav>

<div class="container mt-4">

    <div class="card shadow">
        <div class="card-body">

            <h4 class="mb-3">Daftar Pembayaran Masuk</h4>

            <table class="table table-bordered table-striped">
                <thead class="table-warning">
                    <tr align="center">
                        <th>ID Bayar</th>
                        <th>Nama Penumpang</th>
                        <th>Asal → Tujuan</th>
                        <th>Jumlah</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php while($row = $data->fetch_assoc()){ ?>
                    <tr align="center">

                        <td><?= $row['pembayaran_id'] ?></td>

                        <td><?= $row['nama'] ?> <br>
                            <small><?= $row['email'] ?></small>
                        </td>

                        <td><?= $row['asal'] ?> → <?= $row['tujuan'] ?><br>
                            <small><?= $row['tanggal'] ?> | <?= $row['jam'] ?></small>
                        </td>

                        <td>Rp <?= number_format($row['jumlah'],0,',','.') ?></td>

                        <td><?= $row['metode'] ?></td>

                        <td>
                            <?php if($row['status']=="Valid"){ ?>
                                <span class="badge bg-success">Valid</span>
                            <?php } elseif($row['status']=="Tolak"){ ?>
                                <span class="badge bg-danger">Ditolak</span>
                            <?php } else { ?>
                                <span class="badge bg-warning text-dark">Menunggu</span>
                            <?php } ?>
                        </td>

                        <td>
                            <a href="../uploads/<?= $row['bukti'] ?>" target="_blank">
                                <img src="../uploads/<?= $row['bukti'] ?>" width="60" height="60" style="object-fit:cover; border-radius:8px;">
                            </a>
                        </td>

                        <td>
                            <?php if($row['status']=="Menunggu" || $row['status']=="") { ?>
                                <a href="verifikasi_proses.php?id=<?= $row['pembayaran_id'] ?>&act=valid"
                                   class="btn btn-success btn-sm w-100 mb-1">Valid</a>
                                <a href="verifikasi_proses.php?id=<?= $row['pembayaran_id'] ?>&act=tolak"
                                   class="btn btn-danger btn-sm w-100">Tolak</a>
                            <?php } else { ?>
                                <small>Tidak ada aksi</small>
                            <?php } ?>
                        </td>

                    </tr>
                <?php } ?>
                </tbody>

            </table>

        </div>
    </div>

</div>

</body>
</html>
