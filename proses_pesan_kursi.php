<?php
session_start();
require 'koneksi.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Validasi input POST
$jadwal_id = $_POST['jadwal_id'] ?? null;
$kursi_id = $_POST['kursi_id'] ?? null;

if (!$jadwal_id || !$kursi_id) {
    die("Data pemesanan tidak lengkap.");
}

$user_id = (int) $_SESSION['user_id'];

try {
    // Ambil penumpang_id
    $stmt = $conn->prepare("SELECT penumpang_id FROM penumpang WHERE user_id = ? LIMIT 1");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $pen = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$pen) {
        die("Data penumpang tidak ditemukan. Pastikan profil penumpang sudah dibuat.");
    }
    $penumpang_id = (int) $pen['penumpang_id'];

    // TRANSAKSI: cegah double booking kursi
    $conn->begin_transaction();

    // Kunci kursi dulu (butuh InnoDB)
    $stmt = $conn->prepare("SELECT status FROM kursi WHERE kursi_id = ? FOR UPDATE");
    $stmt->bind_param("i", $kursi_id);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$row) {
        $conn->rollback();
        die("Kursi tidak ditemukan.");
    }

    if ($row['status'] === "Terisi") {
        $conn->rollback();
        echo "<script>alert('Kursi sudah terisi! Pilih kursi lain.'); window.location='pilih_kursi.php?jadwal_id=$jadwal_id';</script>";
        exit;
    }

    // Update kursi hanya jika belum terisi
    $stmt = $conn->prepare("UPDATE kursi SET status='Terisi' WHERE kursi_id=? AND status<>'Terisi'");
    $stmt->bind_param("i", $kursi_id);
    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    if ($affected === 0) {
        $conn->rollback();
        echo "<script>alert('Kursi baru saja terisi oleh orang lain. Silakan pilih kursi lain.'); window.location='pilih_kursi.php?jadwal_id=$jadwal_id';</script>";
        exit;
    }

    // Simpan pemesanan
    $tanggal_pesan = date("Y-m-d");
    $status_pesanan = "Menunggu Pembayaran";

    $stmt = $conn->prepare("
        INSERT INTO pemesanan_tiket (penumpang_id, jadwal_id, kursi_id, tanggal_pesan, status_pesanan)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("iiiss", $penumpang_id, $jadwal_id, $kursi_id, $tanggal_pesan, $status_pesanan);
    $stmt->execute();
    $stmt->close();

    $conn->commit();

    echo "<script>
        alert('Tiket berhasil dipesan!');
        window.location='dashboard_penumpang.php';
    </script>";
} catch (Throwable $e) {
    if ($conn && $conn->errno === 0) {
        // no-op
    } else {
        // no-op
    }
    if ($conn)
        $conn->rollback();
    die("Terjadi kesalahan server: " . htmlspecialchars($e->getMessage()));
}