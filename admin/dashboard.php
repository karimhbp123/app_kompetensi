<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
  header("Location: ../user/dashboard.php");
  exit;
}

include '../config/db.php';

// Ambil total ASN dan Non-ASN
$asn = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total_asn FROM users WHERE role = 'asn'"))['total_asn'];
$nonasn = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total_nonasn FROM users WHERE role = 'nonasn'"))['total_nonasn'];
$total_diklat = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM diklat"))['total'];
$total_jam = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(durasi_jam) AS jam FROM diklat"))['jam'];


$search = isset($_GET['search']) ? mysqli_real_escape_string($koneksi, $_GET['search']) : '';

$log = mysqli_query($koneksi, "
  SELECT d.nama, d.nip, u.role AS status, d.jabatan, 
         d.nama_diklat, d.jenis_diklat, d.jenis_diklat_struktural, d.instansi, 
         d.no_sertifikat, d.tgl_mulai, d.tgl_selesai, d.durasi_jam, d.file_sertifikat
  FROM diklat d 
  JOIN users u ON d.user_id = u.id 
  WHERE d.nama LIKE '%$search%' OR d.nip LIKE '%$search%'
  ORDER BY d.id DESC
  LIMIT 50
");



date_default_timezone_set('Asia/Jakarta');
$hari = date('l');
$tanggal = date('d M Y');
$jam = date('H:i');
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
      margin-left: 220px;
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

    .header-text {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    #imgModal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100vw;
      height: 100vh;
      background-color: rgba(0, 0, 0, 0.8);
      justify-content: center;
      align-items: center;
    }

    #imgModal iframe,
    #imgModal img {
      max-width: 90%;
      max-height: 90%;
      border: 2px solid white;
      border-radius: 10px;
      background: white;
    }

    #imgModal span {
      position: absolute;
      top: 10px;
      right: 20px;
      font-size: 40px;
      color: white;
      cursor: pointer;
    }

    .header-text {
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .welcome {
      font-size: 26px;
      font-weight: 700;
      color: #0f172a;
      margin: 0;
      animation: fadeInDown 0.5s ease;
    }

    .highlight {
      color: #2563eb;
    }

    .subtext {
      font-size: 14px;
      color: #475569;
      font-style: italic;
      animation: fadeInUp 0.5s ease;
    }

    @keyframes fadeInDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
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

    .stat-box {
      display: flex;
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .stat {
      padding: 24px;
      border-radius: 20px;
      background: #ffffff;
      color: #1e293b;
      text-align: left;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
      transition: transform 0.3s ease;
      display: flex;
      align-items: center;
      gap: 16px;
      border-left: 6px solid;
    }

    .stat:hover {
      transform: translateY(-4px);
    }

    .stat .icon {
      font-size: 36px;
    }

    .stat .info {
      display: flex;
      flex-direction: column;
    }

    .stat .info h3 {
      font-size: 26px;
      margin: 0;
    }

    .stat .info p {
      margin: 4px 0 0;
      font-size: 14px;
      color: #64748b;
    }

    .stat.asn {
      border-color: #0284c7;
    }

    .stat.nonasn {
      border-color: #10b981;
    }

    .stat.diklat {
      border-color: #9333ea;
    }

    .stat.durasi {
      border-color: #f59e0b;
    }

    @media screen and (max-width: 768px) {
      .stat-grid {
        grid-template-columns: 1fr;
      }
    }

    .card {
      background: #ffffff;
      padding: 24px;
      border-radius: 16px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
      margin-top: 24px;
    }

    .card-title {
      font-size: 20px;
      font-weight: bold;
      margin-bottom: 16px;
      color: #1e293b;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .table-wrapper {
      overflow-x: auto;
    }

    .modern-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 14px;
    }

    .modern-table th,
    .modern-table td {
      padding: 12px 10px;
      border-bottom: 1px solid #e5e7eb;
      border: 1px solid #ccc;
      text-align: left;
      white-space: nowrap;
    }

    .modern-table th {
      background-color: #e0e7ff;
      color: #1e3a8a;
      font-weight: 600;
    }

    .modern-table tr:hover {
      background-color: #f9fafb;
    }

    .btn-link {
      color: #2563eb;
      text-decoration: none;
      font-weight: 500;
    }

    .btn-link:hover {
      text-decoration: underline;
    }

    .text-muted {
      color: #9ca3af;
      font-style: italic;
    }

    .badge {
      padding: 4px 10px;
      border-radius: 9999px;
      font-size: 12px;
      font-weight: 600;
      display: inline-block;
    }

    .badge-asn {
      background-color: #22c55e;
      color: white;
    }

    .badge-nonasn {
      background-color: #3b82f6;
      color: white;
    }


    a {
      text-decoration: none;
    }


    @media screen and (max-width: 768px) {
      .content {
        margin-left: 0;
        padding: 20px;
      }

      .stat-box {
        flex-direction: column;
      }
    }

    .quick-btn {
      display: inline-block;
      background: #2563eb;
      color: white;
      padding: 10px 20px;
      border-radius: 12px;
      font-size: 14px;
      text-decoration: none;
      transition: background 0.3s ease;
    }

    .quick-btn:hover {
      background: #1d4ed8;
    }

    .badge-view {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 4px;
      padding: 4px 10px;
      font-size: 12px;
      font-weight: 500;
      color: #ffffff;
      background-color: #3b82f6;
      border-radius: 999px;
      text-decoration: none;
      line-height: 1;
      box-shadow: 0 1px 3px rgba(59, 130, 246, 0.15);
      transition: all 0.2s ease-in-out;
    }

    .badge-view:hover {
      background-color: #2563eb;
      transform: scale(1.02);
      box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
    }
  </style>
</head>

<body>

  <?php include 'sidebar.php'; ?>

  <div class="content">
    <div class="header-box">
      <div class="header-content">
        <div class="header-text">
          <h2 class="welcome">👋<span class="highlight">Hai Admin!</span></h2>
          <p class="subtext">Selamat datang kembali di dashboard admin. Yuk produktif hari ini 💪</p>
        </div>
        <div class="clock-box">
          <div class="date" id="tanggal"></div>
          <div class="time" id="jam"></div>
        </div>
      </div>
    </div>
    <div class="stat-grid">
      <div class="stat asn">
        <div class="icon">👩‍💼</div>
        <div class="info">
          <h3><?= $asn ?></h3>
          <p>Total Pegawai ASN</p>
        </div>
      </div>
      <div class="stat nonasn">
        <div class="icon">👨‍🔧</div>
        <div class="info">
          <h3><?= $nonasn ?></h3>
          <p>Total Pegawai Non-ASN</p>
        </div>
      </div>
      <div class="stat diklat">
        <div class="icon">📚</div>
        <div class="info">
          <h3><?= $total_diklat ?></h3>
          <p>Total Kegiatan Diklat</p>
        </div>
      </div>
      <div class="stat durasi">
        <div class="icon">⏱️</div>
        <div class="info">
          <h3><?= $total_jam ?> jam</h3>
          <p>Total Jam Pelatihan</p>
        </div>
      </div>
    </div>

    <div class="card">
      <div class="card-title" style="justify-content: space-between;">
        <div><i class="fas fa-history"></i> Aktivitas Diklat Terbaru</div>
        <form method="get" id="searchForm" style="display: flex; gap: 8px; align-items: center;">
          <div style="position: relative;">
            <input type="text" name="search" id="searchInput" placeholder="Cari Nama atau NIP..." value="<?= htmlspecialchars($search) ?>" style="padding: 6px 30px 6px 10px; border-radius: 8px; border: 1px solid #ccc; font-size: 13px;">
            <?php if ($search): ?>
              <a href="dashboard.php" id="clearSearch" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); color: #999; text-decoration: none; font-weight: bold;">&times;</a>
            <?php endif; ?>
          </div>

        </form>
      </div>

      <div class="table-wrapper">
        <table class="modern-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Nama</th>
              <th>NIP</th>
              <th>Jenis Diklat</th>
              <th>Jenis Diklat Struktural</th>
              <th>Jabatan</th>
              <th>Nama Diklat</th>
              <th>Institusi Penyelenggara</th>
              <th>No Sertifikat</th>
              <th>Mulai</th>
              <th>Selesai</th>
              <th>Durasi</th>
              <th>Sertifikat</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php $no = 1;
            while ($row = mysqli_fetch_assoc($log)): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($row['nama']) ?></td>
                <td><?= htmlspecialchars($row['nip']) ?></td>
                <td><?= htmlspecialchars($row['jenis_diklat']) ?></td>
                <td><?= htmlspecialchars($row['jenis_diklat_struktural']) ?></td>
                <td><?= htmlspecialchars($row['jabatan']) ?></td>
                <td><?= htmlspecialchars($row['nama_diklat']) ?></td>
                <td><?= htmlspecialchars($row['instansi']) ?></td>
                <td><?= htmlspecialchars($row['no_sertifikat']) ?></td>
                <td><?= date('d M Y', strtotime($row['tgl_mulai'])) ?></td>
                <td><?= date('d M Y', strtotime($row['tgl_selesai'])) ?></td>
                <td><?= htmlspecialchars($row['durasi_jam']) ?> jam</td>
                <td>
                  <?php if ($row['file_sertifikat']) : ?>
                    <a href="#" class="badge-view" data-img="../sertifikat/<?= htmlspecialchars(preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($row['nama']))) ?>/<?= htmlspecialchars($row['file_sertifikat']) ?>">
                      <span class="icon">🔍</span>
                      <span class="text">View</span>
                    </a>
                  <?php else : ?>
                    <span style="color: #999;">Tidak ada</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php if ($row['status'] === 'asn'): ?>
                    <span class="badge badge-asn">ASN</span>
                  <?php else: ?>
                    <span class="badge badge-nonasn">NON-ASN</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div id="imgModal">
    <span onclick="closeModal()">&times;</span>
    <img id="modalImg" src="" style="display: none;">
    <iframe id="modalPdf" src="" width="80%" height="90%" style="border: none; display: none;"></iframe>
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

    const viewLinks = document.querySelectorAll('.badge-view');
    const modal = document.getElementById('imgModal');
    const modalImg = document.getElementById('modalImg');
    const modalPdf = document.getElementById('modalPdf');

    viewLinks.forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const filePath = this.getAttribute('data-img');
        const extension = filePath.split('.').pop().toLowerCase();

        modalImg.style.display = 'none';
        modalPdf.style.display = 'none';
        modalImg.src = '';
        modalPdf.src = '';

        if (extension === 'pdf') {
          modalPdf.src = filePath;
          modalPdf.style.display = 'block';
        } else {
          modalImg.src = filePath;
          modalImg.style.display = 'block';
        }

        modal.style.display = 'flex';
      });
    });

    function closeModal() {
      modal.style.display = 'none';
      modalImg.src = '';
      modalPdf.src = '';
      modalImg.style.display = 'none';
      modalPdf.style.display = 'none';
    }

    const searchForm = document.getElementById("searchForm");
    const searchInput = document.getElementById("searchInput");

    searchForm.addEventListener('submit', function(e) {
      if (searchInput.value.trim() === '') {
        window.location.href = 'dashboard.php'; // reset ke halaman tanpa query
        e.preventDefault();
      }
    });
  </script>

</body>

</html>