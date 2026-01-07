<?php
session_start();
require 'koneksi.php';

// Tampilkan error saat debugging (boleh dimatikan nanti)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Validasi input
$jadwal_id = $_POST['jadwal_id'] ?? null;
$kursi_id = $_POST['kursi_id'] ?? null;

if (!$jadwal_id || !$kursi_id) {
    die("Data pemesanan tidak lengkap.");
}

$user_id = (int) $_SESSION['user_id'];
$jadwal_id = (int) $jadwal_id;
$kursi_id = (int) $kursi_id;

try {
    // Ambil penumpang_id
    $stmt = $conn->prepare("SELECT penumpang_id FROM penumpang WHERE user_id=? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $pen = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$pen) {
        die("Data penumpang tidak ditemukan. Pastikan user sudah punya data penumpang.");
    }
    $penumpang_id = (int) $pen['penumpang_id'];

    // Mulai transaksi untuk mencegah double booking
    $conn->begin_transaction();

    // Ambil status & nomor kursi
    $stmt = $conn->prepare("SELECT status, nomor_kursi FROM kursi WHERE kursi_id=? FOR UPDATE");
    $stmt->bind_param("i", $kursi_id);
    $stmt->execute();
    $kursi = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$kursi) {
        $conn->rollback();
        die("Kursi tidak ditemukan.");
    }

    // Status sesuai ENUM: KOSONG / DIPESAN
    if ($kursi['status'] === 'DIPESAN') {
        $conn->rollback();
        echo "<script>alert('Kursi sudah dipesan! Pilih kursi lain.'); window.location='pilih_kursi.php?jadwal_id=$jadwal_id';</script>";
        exit;
    }

    // Update kursi menjadi DIPESAN
    $stmt = $conn->prepare("UPDATE kursi SET status='DIPESAN' WHERE kursi_id=? AND status<>'DIPESAN'");
    $stmt->bind_param("i", $kursi_id);
    $stmt->execute();

    if ($stmt->affected_rows === 0) {
        $stmt->close();
        $conn->rollback();
        echo "<script>alert('Kursi baru saja dipesan orang lain. Silakan pilih kursi lain.'); window.location='pilih_kursi.php?jadwal_id=$jadwal_id';</script>";
        exit;
    }
    $stmt->close();

    // Insert pemesanan_tiket
    // status_pesanan sesuai ENUM: PENDING / LUNAS / BATAL
    $tanggal_pesan = date("Y-m-d");
    $status_pesanan = "PENDING";
    $nomor_kursi = $kursi['nomor_kursi'];

    $stmt = $conn->prepare("
        INSERT INTO pemesanan_tiket (penumpang_id, jadwal_id, tanggal_pesan, status_pesanan, kursi_id, nomor_kursi)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iissis", $penumpang_id, $jadwal_id, $tanggal_pesan, $status_pesanan, $kursi_id, $nomor_kursi);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    echo "<script>
        alert('Tiket berhasil dipesan!');
        window.location='dashboard_penumpang.php';
    </script>";
} catch (Throwable $e) {
    if ($conn)
        $conn->rollback();
    die("Terjadi kesalahan: " . htmlspecialchars($e->getMessage()));
}