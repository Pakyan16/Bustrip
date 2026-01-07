<?php
session_start();
session_unset(); // Hapus semua data session
session_destroy(); // Hapus session dari server
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Logout | BusTrip</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #f9d423, #ff4e50);
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      font-family: 'Poppins', sans-serif;
    }
    .logout-card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
      width: 90%;
      max-width: 500px;
      padding: 40px;
      text-align: center;
      animation: fadeIn 0.8s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-10px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .bus-icon {
      font-size: 60px;
      color: #ff4e50;
    }
    .btn-login {
      background-color: #28a745;
      color: white;
      border: none;
      transition: 0.3s;
    }
    .btn-login:hover {
      background-color: #218838;
    }
  </style>
</head>
<body>

<div class="logout-card">
  <div class="bus-icon mb-3">🚌</div>
  <h2 class="fw-bold text-danger">Anda Telah Logout</h2>
  <p class="text-secondary mb-4">Terima kasih telah menggunakan layanan <strong>BusTrip</strong>!<br>
  Sampai jumpa di perjalanan berikutnya 🚍</p>

  <a href="login.php" class="btn btn-login px-4 py-2 rounded-pill">
    Kembali ke Login
  </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
