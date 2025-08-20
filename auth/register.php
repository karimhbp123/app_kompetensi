<?php
include '../config/db.php';

$registerError = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
  $nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
  $role = $_POST['role'];

  if ($role === 'asn' || $role === 'nonasn') {
    // Cek apakah NIP sudah terdaftar
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE nip='$nip'");
    if (mysqli_num_rows($cek) > 0) {
      $registerError = 'NIP sudah terdaftar.';
    } else {
      $query = "INSERT INTO users (nama, nip, role) VALUES ('$nama', '$nip', '$role')";
      $result = mysqli_query($koneksi, $query);
      if ($result) {
        header("Location: login.php");
        exit;
      } else {
        $registerError = "Gagal mendaftar: " . mysqli_error($koneksi);
      }
    }
  } else {
    $registerError = "Role tidak valid.";
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>📝 Daftar Pengguna</title>
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

    input,
    select {
      padding: 12px;
      margin-bottom: 16px;
      border-radius: 10px;
      border: none;
      font-size: 16px;
    }

    input:focus,
    select:focus {
      outline: none;
      box-shadow: 0 0 0 2px #1abc9c;
    }

    button {
      padding: 12px;
      border-radius: 10px;
      border: none;
      background-color: #1abc9c;
      color: white;
      font-size: 16px;
      font-weight: bold;
      transition: background 0.3s ease;
    }

    button:hover {
      background-color: #16a085;
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
      color: #1abc9c;
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
    <h2>📝 Daftar Pengguna Baru</h2>

    <?php if (!empty($registerError)) : ?>
      <div class="error-message"><?= $registerError; ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="nama" placeholder="Nama Lengkap dengan gelar ( jika ada )" required>
      <input type="text" name="nip" placeholder="NIP" required>
      <select name="role" required>
        <option value="">-- Pilih Role --</option>
        <option value="asn">ASN</option>
        <option value="nonasn">Non-ASN</option>
      </select>
      <button type="submit">Daftar</button>
    </form>

    <div class="back-link">
      Sudah punya akun? <a href="login.php">Login</a>
    </div>
  </div>

</body>

</html>
