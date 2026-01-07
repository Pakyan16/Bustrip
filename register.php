<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require 'koneksi.php';


$error_msg = "";
$success_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama     = isset($_POST['nama']) ? trim($_POST['nama']) : '';
    $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $no_hp    = isset($_POST['no_hp']) ? trim($_POST['no_hp']) : '';
    $role     = 'PENUMPANG';

    if ($nama === '' || $email === '' || $password === '' || $no_hp === '') {
        $error_msg = "❌ Semua field harus diisi!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("INSERT INTO user (nama, email, password, no_hp, role) VALUES (?, ?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("sssss", $nama, $email, $hashed_password, $no_hp, $role);

            if ($stmt->execute()) {
                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'bustrip.00@gmail.com';
                    $mail->Password   = 'wjnnckkubagirzhv';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;

                    $mail->setFrom('bustrip.00@gmail.com', 'BusTrip');
                    $mail->addAddress($email, $nama);
                    $mail->isHTML(true);
                    $mail->Subject = 'Verifikasi Akun BusTrip';
                    $mail->Body    = "
                        <h3>Halo, $nama!</h3>
                        <p>Terima kasih telah mendaftar di BusTrip.</p>
                        <p>Klik link berikut untuk verifikasi akun Anda:</p>
                        <a href='https://bustrip.site/verify_email.php?email=$email'>Verifikasi Akun</a>
                    ";
                    
                    $mail->send();
                    $success_msg = "Registrasi berhasil! Silakan cek email untuk verifikasi.";
                    header("Location: register.php?status=sukses");
                    exit;
                } catch (Exception $e) {
                    $error_msg = "❌ Email gagal dikirim: {$mail->ErrorInfo}";
                }
            } else {
                $error_msg = "❌ Gagal menyimpan data: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $error_msg = "❌ Terjadi kesalahan query database!";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Register - BusTrip</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: linear-gradient(to right, #ffb347, #ffcc33);
      font-family: 'Poppins', sans-serif;
    }
    .card {
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .btn-bustrip {
      background-color: #ff6600;
      border: none;
      transition: 0.3s;
    }
    .btn-bustrip:hover {
      background-color: #ff8533;
    }
    .logo-text {
      font-weight: 700;
      color: #006400;
      letter-spacing: 1px;
    }

    /* Toast */
    .toast-alert {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 14px 18px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 280px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        animation: slideIn 0.5s ease-out, fadeOut 0.5s 4s forwards;
        font-size: 15px;
        z-index: 99999;
    }
    .toast-icon {
        background: white;
        color: #28a745;
        font-weight: bold;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    @keyframes slideIn {
        from { transform: translateX(120%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes fadeOut {
        to { opacity: 0; transform: translateX(120%); }
    }
  </style>
</head>

<body>

<?php if (isset($_GET['status']) && $_GET['status'] == "sukses"): ?>
<div class="toast-alert">
    <div class="toast-icon">✔</div>
    <div>Registrasi berhasil! Silakan cek email untuk verifikasi.</div>
</div>
<?php endif; ?>

<div class="container vh-100 d-flex justify-content-center align-items-center">

  <div class="card p-4" style="width: 450px;">

    <h3 class="text-center mb-2 logo-text">🚌 BusTrip</h3>
    <h5 class="text-center mb-4 text-dark">Daftar Akun Baru</h5>

    <!-- ERROR MESSAGE -->
    <?php if ($error_msg != ""): ?>
      <div class="alert alert-danger p-2 text-center">
        <?= $error_msg ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="nama" class="form-control" placeholder="Masukkan nama lengkap" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Email</label>
        <input type="email" name="email" class="form-control" placeholder="Masukkan email aktif" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Nomor HP</label>
        <input type="text" name="no_hp" class="form-control" placeholder="Contoh: 081234567890" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required>
      </div>

      <button type="submit" name="register" class="btn btn-bustrip text-white fw-bold w-100">
        Daftar Sekarang
      </button>
    </form>

    <p class="text-center mt-3">
      Sudah punya akun?
      <a href="index.php" class="text-decoration-none text-success fw-semibold">Login di sini</a>
    </p>

  </div>
</div>

</body>
</html>
