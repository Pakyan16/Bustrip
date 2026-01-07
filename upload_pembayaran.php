<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $pemesanan_id = $_POST['pemesanan_id'];
    $metode = $_POST['metode'];
    $jumlah = $_POST['jumlah'];

    // Pastikan file bukti dikirim
    if (!empty($_FILES['bukti']['name'])) {

        // Nama file unik
        $nama_file = time() . "_" . $_FILES['bukti']['name'];
        $lokasi = "uploads/" . $nama_file;

        // Upload file
        move_uploaded_file($_FILES['bukti']['tmp_name'], $lokasi);

        // Cek apakah pembayaran sudah ada (biar tidak duplicate entry)
        $cek = $conn->query("SELECT * FROM pembayaran WHERE pemesanan_id = '$pemesanan_id'");

        if ($cek->num_rows > 0) {
            // Jika sudah ada → UPDATE
            $update = $conn->query("
                UPDATE pembayaran 
                SET metode = '$metode', jumlah = '$jumlah', bukti = '$nama_file'
                WHERE pemesanan_id = '$pemesanan_id'
            ");
        } else {
            // Jika belum ada → INSERT
            $update = $conn->query("
                INSERT INTO pembayaran (pemesanan_id, metode, jumlah, bukti)
                VALUES ('$pemesanan_id', '$metode', '$jumlah', '$nama_file')
            ");
        }

        // Update status pemesanan
        $conn->query("
            UPDATE pemesanan_tiket 
            SET status_pembayaran = 'MENUNGGU VERIFIKASI'
            WHERE pemesanan_id = '$pemesanan_id'
        ");

        if ($update) {
            // ✔ Redirect sesuai permintaan
            header("Location: pemesanan_saya.php?status=sukses");
            exit;
        } else {
            echo "Error: " . $conn->error;
        }

    } else {
        echo "Bukti pembayaran wajib diupload!";
    }

} else {
    header("Location: index.php");
    exit;
}
?>
