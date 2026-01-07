<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $jadwal_id     = $_POST['jadwal_id'];
    $kursi_id      = $_POST['kursi_id'];
    $nomor_kursi   = $_POST['nomor_kursi'];
    $user_id       = $_SESSION['user_id'];

    // Cek status jadwal
    $cekStatus = $conn->query("SELECT status FROM jadwal_keberangkatan WHERE jadwal_id='$jadwal_id'");
    $dataStatus = $cekStatus->fetch_assoc();

    if (!$dataStatus) {
        echo "<script>alert('Jadwal tidak ditemukan!'); window.location='dashboard_penumpang.php';</script>";
        exit;
    }

    if ($dataStatus['status'] === "Berangkat") {
        echo "<script>alert('Tidak dapat memesan! Bus sudah berangkat.'); window.location='dashboard_penumpang.php';</script>";
        exit;
    }

    // Ambil penumpang_id dari user_id
    $queryPenumpang = $conn->query("SELECT penumpang_id FROM penumpang WHERE user_id = '$user_id'");
    $penumpang = $queryPenumpang->fetch_assoc();

    if ($penumpang) {

        $penumpang_id = $penumpang['penumpang_id'];

        // =========================
        // INSERT PEMESANAN TIKET
        // =========================
        $sql = "
            INSERT INTO pemesanan_tiket 
                (penumpang_id, jadwal_id, kursi_id, nomor_kursi, status_pembayaran)
            VALUES
                ('$penumpang_id', '$jadwal_id', '$kursi_id', '$nomor_kursi', 'PENDING')
        ";

        if ($conn->query($sql)) {

            // Update kursi menjadi terisi
            $conn->query("UPDATE kursi SET status='Terisi' WHERE kursi_id='$kursi_id'");

            echo "<script>
                alert('Tiket berhasil dipesan!');
                window.location='dashboard_penumpang.php';
            </script>";
            exit;

        } else {
            echo "<script>
                alert('Gagal memesan tiket!');
                window.location='dashboard_penumpang.php';
            </script>";
            exit;
        }

    } else {
        echo "<script>
            alert('Data penumpang tidak ditemukan!');
            window.location='dashboard_penumpang.php';
        </script>";
        exit;
    }
}
?>
