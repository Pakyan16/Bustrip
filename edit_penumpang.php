<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: kelola_penumpang.php");
    exit;
}

$id = $_GET['id'];

// Ambil data berdasarkan ID
$data = mysqli_query($conn, "
    SELECT p.penumpang_id, p.no_hp, u.nama, u.email 
    FROM penumpang p
    JOIN user u ON p.user_id = u.user_id
    WHERE p.penumpang_id='$id'
")->fetch_assoc();

if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

// Jika tombol simpan ditekan
if (isset($_POST['update'])) {
    $no_hp = $_POST['no_hp'];

    mysqli_query($conn, "
        UPDATE penumpang 
        SET no_hp='$no_hp'
        WHERE penumpang_id='$id'
    ");

    echo "<script>
    alert('Data penumpang berhasil diperbarui!');
    window.location='kelola_penumpang.php';
    </script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Penumpang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card shadow" style="max-width: 500px; margin:auto;">
        <div class="card-header bg-warning text-white">
            <h4 class="mb-0">Edit Penumpang</h4>
        </div>

        <div class="card-body">

            <form method="POST">

                <div class="mb-3">
                    <label class="form-label">Nama User</label>
                    <input type="text" class="form-control" value="<?= $data['nama'] ?>" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email User</label>
                    <input type="text" class="form-control" value="<?= $data['email'] ?>" disabled>
                </div>

                <div class="mb-3">
                    <label class="form-label">No HP</label>
                    <input type="text" name="no_hp" class="form-control" value="<?= $data['no_hp'] ?>" required>
                </div>

                <button type="submit" name="update" class="btn btn-success">Simpan</button>
                <a href="kelola_penumpang.php" class="btn btn-secondary">Kembali</a>

            </form>

        </div>
    </div>
</div>

</body>
</html>
