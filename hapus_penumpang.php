<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    header("Location: kelola_penumpang.php");
    exit;
}

$id = $_GET['id'];

// Hapus data penumpang
mysqli_query($conn, "DELETE FROM penumpang WHERE penumpang_id='$id'");

echo "<script>
alert('Penumpang berhasil dihapus!');
window.location='kelola_penumpang.php';
</script>";
?>
