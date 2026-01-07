<?php
include 'koneksi.php';

// Daftar bus + jumlah kursi
$bus_list = [
    1 => 25, // Bus Ekonomi
    2 => 20, // Bus AC Eksekutif
    3 => 15  // Bus VIP
];

foreach ($bus_list as $bus_id => $jumlah_kursi) {

    echo "<b>Bus ID: $bus_id — Generate $jumlah_kursi kursi</b><br>";

    for ($i = 1; $i <= $jumlah_kursi; $i++) {

        $nomor = "Kursi " . $i;

        // INSERT Kursi
        $sql = "INSERT INTO kursi (bus_id, nomor_kursi, status)
                VALUES ('$bus_id', '$nomor', 'KOSONG')";

        if ($conn->query($sql) === TRUE) {
            echo "✔ $nomor berhasil dibuat<br>";
        } else {
            echo "❌ ERROR: " . $conn->error . "<br>";
        }
    }

    echo "<hr>";
}

echo "<br><b>SELESAI — Semua kursi berhasil di-generate.</b>";
