<?php
// ===== Pilih Kursi Realistik =====
// Hanya tampilan yang diubah
session_start();
include 'koneksi.php';

if (!isset($_GET['bus_id']) || !isset($_GET['jadwal_id'])) {
    echo "Data bus atau jadwal tidak ditemukan.";
    exit;
}

$bus_id = intval($_GET['bus_id']);
$jadwal_id = intval($_GET['jadwal_id']);

// Ambil data kursi
$qKursi = $conn->query("SELECT kursi_id, nomor_kursi, status 
                        FROM kursi 
                        WHERE bus_id = $bus_id 
                        ORDER BY nomor_kursi ASC");

$kursi = [];
while ($row = $qKursi->fetch_assoc()) {
    $kursi[] = $row;
}

// Ambil kursi yang sudah dipesan untuk jadwal ini
$qBooked = $conn->query("SELECT kursi_id 
                         FROM pemesanan_tiket 
                         WHERE jadwal_id = $jadwal_id");

$kursiBooked = [];
while ($b = $qBooked->fetch_assoc()) {
    $kursiBooked[] = $b['kursi_id'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pilih Kursi - Realistik</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body { background: #f7f3e9; font-family: 'Poppins', sans-serif; }

/* --- Realistic Bus Seat Style --- */
.bus-container {
    width: 500px;
    background: #e8e6df;
    padding: 20px;
    border-radius: 20px;
    margin: auto;
    border: 4px solid #c9c6bd;
}

.bus-front {
    width: 100%;
    text-align: center;
    padding-bottom: 10px;
    font-weight: 700;
    color: #5a5a5a;
}

.driver-seat {
    width: 70px;
    height: 70px;
    background: #444;
    color: white;
    border-radius: 10px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

.bus-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
}

.seat-group { display: flex; gap: 15px; }

.seat {
    width: 65px;
    height: 65px;
    background: #ff9900;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: white;
    font-size: 18px;
    font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    transition: 0.2s;
}

.seat::after {
    content: "";
    width: 90%;
    height: 8px;
    background: rgba(0,0,0,0.25);
    border-radius: 0 0 10px 10px;
    margin-top: 5px;
}

.seat:hover { background: #d98200; }

.seat.terisi { 
    background: #777 !important; 
    cursor: not-allowed; 
}

.seat.terpilih { 
    background: #28a745 !important; 
}

.aisle { width: 70px; }

#kursiDipilih { font-size: 18px; }
</style>
</head>
<body>
<div class="container mt-4">
    <h3 class="fw-bold text-success text-center">Pilih Kursi Bus</h3>

    <div class="bus-container mt-4">
        <div class="bus-front">Bagian Depan Bus</div>
        <div class="driver-seat">Sopir</div>

        <?php 
        $i = 0; 
        $total = count($kursi);

        while ($i < $total):
        ?>
        <div class="bus-row">
            <div class="seat-group">
                <?php for ($x = 0; $x < 2; $x++): 
                    if ($i < $total): 
                        $k = $kursi[$i];

                        // Tentukan apakah kursi sudah dibooking
                        $isBooked = in_array($k['kursi_id'], $kursiBooked) 
                                     || strtolower($k['status']) === 'terisi';
                ?>
                    <div class="seat <?= $isBooked ? 'terisi' : '' ?>"
                        data-kursi-id="<?= $k['kursi_id'] ?>"
                        data-nomor="<?= $k['nomor_kursi'] ?>">
                        <?= $k['nomor_kursi'] ?>
                    </div>
                <?php 
                    $i++; 
                    endif; 
                endfor; ?>
            </div>

            <div class="aisle"></div>

            <div class="seat-group">
                <?php for ($x = 0; $x < 2; $x++): 
                    if ($i < $total): 
                        $k = $kursi[$i];

                        $isBooked = in_array($k['kursi_id'], $kursiBooked) 
                                     || strtolower($k['status']) === 'terisi';
                ?>
                    <div class="seat <?= $isBooked ? 'terisi' : '' ?>"
                        data-kursi-id="<?= $k['kursi_id'] ?>"
                        data-nomor="<?= $k['nomor_kursi'] ?>">
                        <?= $k['nomor_kursi'] ?>
                    </div>
                <?php 
                    $i++; 
                    endif; 
                endfor; ?>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <p id="kursiDipilih" class="mt-3 fw-bold text-primary text-center">Belum memilih kursi</p>

    <form action="proses_pesan_kursi.php" method="POST" class="text-center mt-3">
        <input type="hidden" name="kursi_id" id="kursi_id">
        <input type="hidden" name="nomor_kursi" id="nomor_kursi">
        <input type="hidden" name="jadwal_id" value="<?= $jadwal_id ?>">
        <button class="btn btn-success px-4">Lanjutkan</button>
    </form>
</div>

<script>
const seats = document.querySelectorAll(".seat");
const inputKursiID = document.getElementById("kursi_id");
const inputNomor = document.getElementById("nomor_kursi");
const textKursi = document.getElementById("kursiDipilih");

seats.forEach(seat => {
    if (!seat.classList.contains("Terisi") && !seat.classList.contains("terisi")) {
        seat.addEventListener("click", function() {
            seats.forEach(s => s.classList.remove("terpilih"));
            this.classList.add("terpilih");

            inputKursiID.value = this.dataset.kursiId;
            inputNomor.value = this.dataset.nomor;

            textKursi.textContent = "Kursi dipilih: " + this.dataset.nomor;
        });
    }
});
</script>
</body>
</html>
