<?php
session_start();
if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
  if ($_SESSION['role'] === 'admin') {
    header("Location: admin/dashboard.php");
    exit;
  } else {
    header("Location: user/dashboard.php");
    exit;
  }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>🏫 Aplikasi Kompetensi Pegawai</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
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
      background-image: url('assets/rs_image.jpg');
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
      background-color: rgba(0, 0, 0, 0.30);
      backdrop-filter: blur(2px);
      -webkit-backdrop-filter: blur(3px);
      z-index: -2;
    }

    .overlay {
      backdrop-filter: blur(8px);
      background-color: rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      padding: 50px 40px;
      max-width: 450px;
      width: 100%;
      text-align: center;
      color: #fff;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
      animation: fadeIn 1s ease-in-out;
      position: relative;
      z-index: 1;
      margin: auto;
      top: 50%;
      transform: translateY(-50%);
    }

    h1 {
      font-size: 26px;
      margin-bottom: 20px;
      color: #ffffff;
    }

    .overlay p {
      font-size: 16px;
      margin-bottom: 30px;
      color: #f1f1f1;
    }

    .buttons {
      display: flex;
      justify-content: center;
      gap: 20px;
      flex-wrap: wrap;
    }

    .buttons a {
      background-color: rgba(255, 255, 255, 0.15);
      color: white;
      border: 1px solid white;
      padding: 12px 24px;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
      transition: all 0.3s ease;
    }

    .buttons a:hover {
      background-color: white;
      color: #2c3e50;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(30px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 600px) {
      .overlay {
        padding: 30px 20px;
      }

      h1 {
        font-size: 22px;
      }

      .buttons {
        flex-direction: column;
      }
    }
  </style>
</head>

<body>

  <div class="bg-image"></div>
  <div class="bg-dark-blur"></div>

  <div class="overlay">
    <h1>🏫 Aplikasi Kompetensi Pegawai</h1>
    <p>Selamat datang! Silakan login atau daftar untuk mengelola data pengembangan kompetensi Anda.</p>
    <div class="buttons">
      <a href="auth/login.php">🔐 Login</a>
      <a href="auth/register.php">📝 Register</a>
    </div>
  </div>

</body>

</html>
