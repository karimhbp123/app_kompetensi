<?php
session_start();
include '../config/db.php';

$loginError = '';

if (isset($_POST['login'])) {
  $nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
  $query = mysqli_query($koneksi, "SELECT * FROM users WHERE nip='$nip'");
  $data = mysqli_fetch_assoc($query);

  if ($data) {
    $_SESSION['user_id'] = $data['id'];
    $_SESSION['role'] = $data['role'];
    $_SESSION['nama'] = $data['nama'];
    $_SESSION['nip'] = $data['nip'];

    if ($data['role'] === 'admin') {
      header('Location: ../admin/dashboard.php');
    } else {
      header('Location: ../user/dashboard.php');
    }
    exit;
  } else {
    $loginError = 'NIP tidak ditemukan atau tidak valid.';
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>🔐 Login Pengguna</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      padding: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      height: 100vh;
      overflow: hidden;
      position: relative;
    }

    .bg-image {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-image: url('../assets/rs_image.jpg');
      background-size: cover;
      background-position: center;
      z-index: -3;
    }

    .bg-dark-blur {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.3);
      backdrop-filter: blur(2px);
      -webkit-backdrop-filter: blur(3px);
      z-index: -2;
    }

    .form-container {
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.3);
      backdrop-filter: blur(15px);
      -webkit-backdrop-filter: blur(15px);
      padding: 40px;
      border-radius: 16px;
      width: 90%;
      max-width: 420px;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.25);
      color: #fff;
      text-align: center;
      position: relative;
      z-index: 1;
      margin: auto;
      top: 50%;
      transform: translateY(-50%);
    }

    .form-container h2 {
      margin-bottom: 25px;
      font-size: 24px;
      color: #ffffff;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    input {
      padding: 12px;
      margin-bottom: 16px;
      border-radius: 10px;
      border: none;
      font-size: 16px;
    }

    input:focus {
      outline: none;
      box-shadow: 0 0 0 2px #3498db;
    }

    button {
      padding: 12px;
      border-radius: 10px;
      border: none;
      background-color: #3498db;
      color: white;
      font-size: 16px;
      font-weight: bold;
      transition: background 0.3s ease;
    }

    button:hover {
      background-color: #2c82c9;
      cursor: pointer;
    }

    .back-link {
      margin-top: 20px;
      font-size: 14px;
    }

    .back-link a {
      color: #ffffff;
      font-weight: bold;
      text-decoration: none;
    }

    .back-link a:hover {
      color: #3498db;
    }

    .error-message {
      background-color: rgba(255, 0, 0, 0.15);
      border: 1px solid rgba(255, 0, 0, 0.4);
      padding: 10px;
      margin-bottom: 16px;
      border-radius: 8px;
      color: #ffe0e0;
      font-size: 14px;
    }
  </style>
</head>

<body>

  <div class="bg-image"></div>
  <div class="bg-dark-blur"></div>

  <div class="form-container">
    <h2>🔐 Masuk ke Aplikasi</h2>

    <?php if (!empty($loginError)) : ?>
      <div class="error-message"><?= $loginError; ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="nip" placeholder="Masukkan NIP Anda" required>
      <button type="submit" name="login">Masuk</button>
    </form>

    <div class="back-link">
      Belum punya akun? <a href="register.php">Daftar</a>
    </div>
  </div>

</body>

</html>
