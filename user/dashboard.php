<?php
session_start();
include '../config/db.php';
$current = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit();
}

date_default_timezone_set('Asia/Jakarta');

$user_id = $_SESSION['user_id'];

$q_user = mysqli_query($koneksi, "SELECT nama, nip FROM users WHERE id='$user_id'");
$user_data = mysqli_fetch_assoc($q_user);
$nama_user = $user_data ? ucwords(strtolower($user_data['nama'])) : 'User';
$nip_user = $user_data ? $user_data['nip'] : '';

$q_tahun = mysqli_query($koneksi, "
    SELECT DISTINCT YEAR(tgl_mulai) AS tahun
    FROM diklat
    WHERE user_id='$user_id'
    ORDER BY tahun DESC
");
$tahun_list = [];
while ($row = mysqli_fetch_assoc($q_tahun)) {
  if (!empty($row['tahun'])) {
    $tahun_list[] = $row['tahun'];
  }
}

$tahun_pilih = isset($_GET['tahun']) ? $_GET['tahun'] : 'all';
$where_tahun = '';
if ($tahun_pilih !== 'all') {
  $tahun_int = intval($tahun_pilih);
  $where_tahun = "AND YEAR(tgl_mulai) = '$tahun_int'";
}

$q_diklat = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total_diklat,
           COALESCE(SUM(durasi_jam), 0) AS total_jam
    FROM diklat
    WHERE user_id = '$user_id' $where_tahun
");
$diklat_data = mysqli_fetch_assoc($q_diklat);
$total_diklat = $diklat_data['total_diklat'];
$total_jam = $diklat_data['total_jam'];

$q_sertifikat = mysqli_query($koneksi, "
    SELECT COUNT(*) AS total_sertifikat
    FROM diklat
    WHERE user_id = '$user_id'
      AND file_sertifikat IS NOT NULL
      AND file_sertifikat <> ''
      $where_tahun
");
$sertifikat_data = mysqli_fetch_assoc($q_sertifikat);
$total_sertifikat = $sertifikat_data['total_sertifikat'];

$q_last = mysqli_query($koneksi, "
    SELECT nama_diklat, tgl_mulai
    FROM diklat
    WHERE user_id = '$user_id'
    ORDER BY tgl_mulai DESC
    LIMIT 1
");
$last_diklat = mysqli_fetch_assoc($q_last);

?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Dashboard User</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f1f5f9;
      color: #1e293b;
    }

    .content {
      margin-left: 235px;
      padding: 40px 30px;
    }

    .header-box {
      background: #ffffff;
      padding: 28px 32px;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
      margin-bottom: 32px;
    }

    .header-content {
      display: flex;
      justify-content: space-between;
      align-items: center;
      flex-wrap: wrap;
    }

    .clock-box {
      text-align: right;
    }

    .clock-box .date {
      font-size: 14px;
      color: #475569;
      margin-bottom: 4px;
    }

    .clock-box .time {
      font-size: 20px;
      font-weight: bold;
      color: #1e40af;
      font-family: 'Courier New', Courier, monospace;
    }

    .welcome {
      font-size: 26px;
      font-weight: 700;
      color: #0f172a;
    }

    .highlight {
      color: #2563eb;
    }

    .subtext {
      font-size: 14px;
      color: #475569;
      font-style: italic;
    }

    .stat-wrapper {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 24px;
      align-items: stretch;
    }

    .stat-card {
      background: white;
      border-radius: 16px;
      padding: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
      transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .stat-value {
      font-size: 36px;
      font-weight: 800;
      margin: 0;
      line-height: 1.2;
      text-align: left;
      background: linear-gradient(90deg, #2563eb, #60a5fa);
      background-clip: text;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .stat-card:hover {
      transform: translateY(-6px) scale(1.02);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.12);
    }

    .stat-card-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }

    .stat-title {
      font-size: 16px;
      font-weight: 600;
    }

    .blue {
      border-left: 6px solid #2563eb;
    }

    .green {
      border-left: 6px solid #16a34a;
    }

    .green .stat-value {
      background: linear-gradient(90deg, #16a34a, #4ade80);
      background-clip: text;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .orange {
      border-left: 6px solid #f97316;
    }

    .orange .stat-value {
      background: linear-gradient(90deg, #f97316, #fdba74);
      background-clip: text;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .purple {
      border-left: 6px solid #7c3aed;
    }

    .purple .stat-value {
      background: linear-gradient(90deg, #7c3aed, #c084fc);
      background-clip: text;
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .select-year-mini {
      padding: 4px 8px;
      font-size: 12px;
      border-radius: 6px;
      border: 1px solid #cbd5e1;
      background: #f9fafb;
      cursor: pointer;
    }

    .select-year-mini:hover {
      border-color: #2563eb;
      box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
    }
  </style>
</head>

<body>
  <?php include 'sidebar_user.php'; ?>
  <div class="content">
    <div class="header-box">
      <div class="header-content">
        <div class="header-text">
          <h2 class="welcome">👋 <span class="highlight"><?= htmlspecialchars($nama_user) ?> (<?= htmlspecialchars($nip_user) ?>)</span></h2>
          <p class="subtext">Selamat datang kembali di dashboard user. Yuk produktif hari ini 💪</p>
        </div>
        <div class="clock-box">
          <div class="date" id="tanggal"></div>
          <div class="time" id="jam"></div>
        </div>
      </div>
    </div>

    <div class="stat-wrapper">
      <div class="stat-card blue">
        <div class="stat-card-header">
          <span class="stat-title">📊 Total Diklat <?= ($tahun_pilih === 'all') ? '' : '(' . $tahun_pilih . ')' ?></span>
          <form method="GET">
            <select name="tahun" onchange="this.form.submit()" class="select-year-mini">
              <option value="all" <?= ($tahun_pilih === 'all') ? 'selected' : '' ?>>Semua</option>
              <?php foreach ($tahun_list as $t): ?>
                <option value="<?= $t ?>" <?= ($t == $tahun_pilih) ? 'selected' : '' ?>><?= $t ?></option>
              <?php endforeach; ?>
            </select>
          </form>
        </div>
       <p class="stat-value"><?= $total_diklat ?> diklat</p>
      </div>

      <div class="stat-card green">
        <div class="stat-card-header">
          <span class="stat-title">⏳ Total Durasi Diklat <?= ($tahun_pilih === 'all') ? '' : '(' . $tahun_pilih . ')' ?></span>
          <form method="GET">
            <select name="tahun" onchange="this.form.submit()" class="select-year-mini">
              <option value="all" <?= ($tahun_pilih === 'all') ? 'selected' : '' ?>>Semua</option>
              <?php foreach ($tahun_list as $t): ?>
                <option value="<?= $t ?>" <?= ($t == $tahun_pilih) ? 'selected' : '' ?>><?= $t ?></option>
              <?php endforeach; ?>
            </select>
          </form>
        </div>
        <p class="stat-value"><?= $total_jam ?> jam</p>
      </div>

      <div class="stat-card orange">
        <div class="stat-card-header">
          <span class="stat-title">📜 Total Sertifikat</span>
          <form method="GET">
            <select name="tahun" onchange="this.form.submit()" class="select-year-mini">
              <option value="all" <?= ($tahun_pilih === 'all') ? 'selected' : '' ?>>Semua</option>
              <?php foreach ($tahun_list as $t): ?>
                <option value="<?= $t ?>" <?= ($t == $tahun_pilih) ? 'selected' : '' ?>><?= $t ?></option>
              <?php endforeach; ?>
            </select>
          </form>
        </div>
        <p class="stat-value"><?= $total_sertifikat ?> sertifikat</p>
      </div>

      <div class="stat-card purple">
        <div class="stat-card-header">
          <span class="stat-title">📅 Diklat Terakhir</span>
        </div>
        <p class="stat-value"><?= $last_diklat ? $last_diklat['nama_diklat'] : 'Belum ada' ?></p>
        <small><?= $last_diklat ? date('d M Y', strtotime($last_diklat['tgl_mulai'])) : '' ?></small>
      </div>
    </div>

  </div>

  <script>
    function updateClock() {
      const now = new Date();
      const hari = now.toLocaleDateString('id-ID', {
        weekday: 'long'
      });
      const tanggal = now.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
      });
      const jam = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
      document.getElementById("tanggal").textContent = `${hari}, ${tanggal}`;
      document.getElementById("jam").textContent = jam;
    }
    setInterval(updateClock, 1000);
    updateClock();
  </script>
</body>

</html>