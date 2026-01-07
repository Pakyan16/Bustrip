<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ADMIN') {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $update = $conn->query("
        UPDATE jadwal_keberangkatan
        SET status = 'Berangkat'
        WHERE jadwal_id = '$id'
    ");

    if ($update) {
        header("Location: dashboard_admin.php?status=updated");
        exit;
    } else {
        echo "Gagal mengubah status: " . $conn->error;
    }
}
