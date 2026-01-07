<?php
include 'koneksi.php';

// Ambil semua user
$users = mysqli_query($conn, "SELECT * FROM user");

// Ambil semua penumpang
$penumpang = mysqli_query($conn, "
    SELECT p.*, u.nama, u.email 
    FROM penumpang p
    JOIN user u ON p.user_id = u.user_id
");

// Proses tambah
if (isset($_POST['simpan'])) {

    $user_id = $_POST['user_id'];
    $no_hp   = $_POST['no_hp'];

    // Ambil nama & email otomatis
    $q = mysqli_query($conn, "SELECT nama, email FROM user WHERE user_id='$user_id'");
    $d = mysqli_fetch_assoc($q);

    $nama  = $d['nama'];
    $email = $d['email'];

    // ——— Tidak saya ubah sesuai permintaanmu ———
    $query = "INSERT INTO penumpang (user_id, penumpang_id, alamat, no_hp)
              VALUES ('$user_id', '$penumpang_id', '$alamat', '$no_hp')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Penumpang berhasil ditambahkan'); 
              window.location='tambah_penumpang.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

// Proses hapus
if (isset($_GET['hapus'])) {
    $hapus_id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM penumpang WHERE penumpang_id='$hapus_id'");
    echo "<script>alert('Penumpang berhasil dihapus'); window.location='tambah_penumpang.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Penumpang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7f7f7; }
        .sidebar {
            height: 100vh;
            background: #ff8c00;
            padding-top: 20px;
            color: white;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px 20px;
            font-size: 15px;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.2);
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">

        <!-- SIDEBAR -->
        <div class="col-md-2 sidebar">
            <h4 class="text-center mb-4">BusTrip Admin</h4>
            <a href="dashboard.php">🏠 Dashboard</a>
            <a href="kelola_penumpang.php">👥 Kelola Penumpang</a>
            <a href="kelola_jadwal.php">🚌 Kelola Jadwal</a>
            <a href="kelola_pemesanan.php">📄 Kelola Pemesanan</a>
            <a href="verifikasi_pembayaran.php">💳 Verifikasi Pembayaran</a>
            <a href="logout.php">🚪 Logout</a>
        </div>

        <!-- MAIN -->
        <div class="col-md-10 p-4">
            
            <h2 class="text-success mb-4">Tambah Penumpang</h2>

            <!-- FORM TAMBAH -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Pilih User</label>
                            <select name="user_id" class="form-select" required>
                                <option value="">-- Pilih User --</option>
                                <?php while ($u = mysqli_fetch_assoc($users)) { ?>
                                    <option value="<?= $u['user_id']; ?>">
                                        <?= $u['nama'] . " (" . $u['email'] . ")"; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No HP Penumpang</label>
                            <input type="text" name="no_hp" class="form-control" required>
                        </div>

                        <button type="submit" name="simpan" class="btn btn-success">Simpan</button>
                    </form>

                </div>
            </div>

            <!-- TABEL PENUMPANG -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Data Penumpang</h5>
                </div>
                <div class="card-body">
                    
                    <table class="table table-bordered table-striped">
                        <thead class="table-warning">
                            <tr>
                                <th>ID</th>
                                <th>Nama</th>
                                <th>No HP</th>
                                <th>Email</th>
                                <th width="130">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($p = mysqli_fetch_assoc($penumpang)) { ?>
                            <tr>
                                <td><?= $p['penumpang_id']; ?></td>
                                <td><?= $p['nama']; ?></td>
                                <td><?= $p['no_hp']; ?></td>
                                <td><?= $p['email']; ?></td>
                                <td>
                                    <a href="edit_penumpang.php?id=<?= $p['penumpang_id']; ?>" class="btn btn-warning btn-sm">✏ Edit</a>
                                    <a href="tambah_penumpang.php?hapus=<?= $p['penumpang_id']; ?>" 
                                       onclick="return confirm('Hapus data ini?')"
                                       class="btn btn-danger btn-sm">🗑 Hapus</a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>

    </div>
</div>

</body>
</html>
