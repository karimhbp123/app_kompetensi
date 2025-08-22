<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
  header("Location: ../user/dashboard.php");
  exit;
}
include '../config/db.php';

$query = mysqli_query($koneksi, "
  SELECT d.nama, d.nip, d.jabatan, d.nama_diklat, d.jenis_diklat, d.jenis_diklat_struktural, d.instansi,
         d.no_sertifikat, d.tgl_mulai, d.tgl_selesai, d.durasi_jam, d.file_sertifikat, u.nama AS nama_user
  FROM diklat d
  JOIN users u ON d.user_id = u.id
  WHERE u.role = 'asn'
  ORDER BY d.tgl_mulai DESC
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
  <title>Data Pegawai ASN</title>
  <link rel="icon" type="image/png" href="/app_kompetensi/favicon.png">
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
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      justify-content: center;
      align-items: center;
    }

    #imgModal img {
      max-width: 90%;
      max-height: 90%;
      border: 4px solid white;
      border-radius: 10px;
    }

    #imgModal span {
      position: absolute;
      top: 20px;
      right: 30px;
      font-size: 30px;
      color: white;
      cursor: pointer;
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

    .table-container {
      overflow-x: auto;
      background: #fff;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.04);
    }

    .download-btn {
      display: inline-block;
      background-color: #10b981;
      color: white;
      padding: 10px 18px;
      font-size: 14px;
      font-weight: 600;
      border-radius: 8px;
      text-decoration: none;
      transition: background 0.2s ease;
    }

    .download-btn:hover {
      background-color: #059669;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 1200px;
      font-size: 14px;
    }

    th {
      background-color: #e0e7ff;
      color: #1e3a8a;
      font-weight: 600;
      padding: 12px 10px;
      border-bottom: 2px solid #c7d2fe;
      border: 1px solid #ccc;
      text-align: left;
      white-space: nowrap;
    }

    td {
      padding: 10px;
      border-bottom: 1px solid #e5e7eb;
      border: 1px solid #ccc;
      vertical-align: top;
    }

    tr:hover {
      background-color: #f1f5f9;
    }

    .link {
      color: #2563eb;
      text-decoration: none;
      font-weight: 500;
    }

    .link:hover {
      text-decoration: underline;
    }

    .no-sertifikat {
      color: #9ca3af;
      font-style: italic;
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

    th:nth-child(10),
    th:nth-child(11),
    td:nth-child(2),
    td:nth-child(10),
    td:nth-child(11) {
      white-space: nowrap;
      width: 110px;
    }
  </style>
</head>

<body>
  <?php include 'sidebar.php'; ?>
  <div class="content">
    <div class="header-box">
      <div class="header-content">
        <div class="header-text">
          <h2 class="welcome">👋<span class="highlight">Data Pegawai ASN</span></h2>
          <p class="subtext">Selamat datang kembali di data pegawai ASN. Yuk produktif hari ini 💪</p>
        </div>
        <div class="clock-box">
          <div class="date" id="tanggal"></div>
          <div class="time" id="jam"></div>
        </div>
      </div>
    </div>
    <div class="table-container">
      <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
        <a href="download_excel.php?role=asn" class="download-btn">⬇️ Download Excel</a>
      </div>
      <table>
        <thead>
          <tr>
            <th>No</th>
            <th class="nama-col">Nama</th>
            <th>NIP</th>
            <th>Jabatan</th>
            <th>Jenis Diklat</th>
            <th>Jenis Diklat Struktural</th>
            <th>Nama Diklat</th>
            <th>Institusi Penyelenggara</th>
            <th>No Sertifikat</th>
            <th>Tgl Mulai</th>
            <th>Tgl Selesai</th>
            <th>Durasi (jam)</th>
            <th>Sertifikat</th>
          </tr>
        </thead>
        <tbody>
          <?php $no = 1;
          while ($row = mysqli_fetch_assoc($query)): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td class="nama-col"><?= htmlspecialchars($row['nama']) ?></td>
              <td><?= htmlspecialchars($row['nip']) ?></td>
              <td><?= htmlspecialchars($row['jabatan']) ?></td>
              <td><?= htmlspecialchars($row['jenis_diklat']) ?></td>
              <td><?= htmlspecialchars($row['jenis_diklat_struktural']) ?></td>
              <td><?= htmlspecialchars($row['nama_diklat']) ?></td>
              <td><?= htmlspecialchars($row['instansi']) ?></td>
              <td><?= htmlspecialchars($row['no_sertifikat']) ?></td>
              <td><?= date('d M Y', strtotime($row['tgl_mulai'])) ?></td>
              <td><?= date('d M Y', strtotime($row['tgl_selesai'])) ?></td>
              <td><?= $row['durasi_jam'] ?> jam</td>
              <td>
                <?php if ($row['file_sertifikat']) : ?>
                  <a href="#" class="badge-view" data-img="../sertifikat/<?= htmlspecialchars(preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($row['nama_user']))) ?>/<?= htmlspecialchars($row['file_sertifikat']) ?>">
                    <span class="icon">🔍</span>
                    <span class="text">View</span>
                  </a>
                <?php else : ?>
                  <span style="color: #999;">Tidak ada</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
  <div id="imgModal">
    <span onclick="closeModal()">&times;</span>
    <img id="modalImg" src="" style="display: none;">
    <iframe id="modalPdf" style="display: none; width: 90%; height: 90%; border: 4px solid white; border-radius: 10px; background: white;"></iframe>
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

    modal.addEventListener('click', function(e) {
      if (e.target === modal) closeModal();
    });
  </script>
</body>

</html>