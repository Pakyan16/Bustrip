<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // JANGAN tampilkan error di production

session_start();
require 'koneksi.php';

$error_msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['email'], $_POST['password'])) {
        $error_msg = "Input tidak valid";
    } else {

        $email    = trim($_POST['email']);
        $password = $_POST['password'];

        $stmt = $conn->prepare(
            "SELECT user_id, email, password, role, nama 
             FROM user 
             WHERE email = ? AND status = 1 
             LIMIT 1"
        );
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();

            if (password_verify($password, $row['password'])) {

                session_regenerate_id(true);

                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['email']   = $row['email'];
                $_SESSION['role']    = strtoupper($row['role']);
                $_SESSION['nama']    = $row['nama'];

                if ($_SESSION['role'] === 'ADMIN') {
                    header("Location: dashboard_admin.php");
                } else {
                    header("Location: dashboard_penumpang.php");
                }
                exit;

            } else {
                sleep(2);
                $error_msg = "Email atau password salah";
            }

        } else {
            sleep(2);
            $error_msg = "Email atau password salah";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BusTrip</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #ffb347, #ffcc33);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Poppins', sans-serif;
        }

        .login-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0px 6px 20px rgba(0,0,0,0.15);
            padding: 35px 30px;
            width: 380px;
        }

        .logo-text {
            font-size: 28px;
            font-weight: 700;
            color: #006400;
        }

        .btn-login {
            background-color: #ff6600;
            color: white;
            border-radius: 10px;
            width: 100%;
            padding: 10px;
            font-weight: 600;
        }
        .btn-login:hover {
            background-color: #ff8533;
        }

        .form-control {
            border-radius: 10px;
        }
    </style>
</head>
<body>

    <div class="login-card text-center">

        <div class="logo-text mb-2">🚌 BusTrip</div>
        <h6 class="mb-4 text-secondary">Masuk ke akun Anda</h6>

        <!-- ERROR MESSAGE -->
        <?php if ($error_msg != ""): ?>
            <div class="alert alert-danger py-2">
                <?= $error_msg ?>
            </div>
        <?php endif; ?>
        <!-- END ERROR -->

        <form method="POST" action="">
            <div class="mb-3 text-start">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required placeholder="Masukkan email">
            </div>

            <div class="mb-3 text-start">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
            </div>

            <button type="submit" class="btn btn-login mt-2">Login</button>
        </form>

        <p class="mt-3 text-muted">
            Belum punya akun?
            <a href="register.php" class="text-decoration-none fw-semibold text-success">Daftar di sini</a>
        </p>

    </div>

</body>
</html>
