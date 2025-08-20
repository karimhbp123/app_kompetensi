<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id'])) header("Location: ../auth/login.php");

// Ambil semua user
$users = mysqli_query($koneksi, "SELECT id, nama FROM users WHERE role != 'admin' ORDER BY nama ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>➕ Tambah Sertifikat User</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
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

    .page-title {
      font-size: 28px;
      font-weight: 700;
      color: #0f172a;
      margin: 0;
      animation: fadeInDown 0.4s ease;
    }

    .page-subtitle {
      font-size: 15px;
      color: #64748b;
      margin-top: 6px;
      font-style: italic;
      animation: fadeInUp 0.4s ease;
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

    .container {
      width: 100%;
      margin: 20px 0;
      background: #fff;
      padding: 20px 30px;
      border-radius: 8px;
      box-sizing: border-box;
    }

    .form-title {
      font-size: 22px;
      font-weight: 600;
      color: #1e293b;
      text-align: center;
      margin-bottom: 24px;
      border-bottom: 1px solid #e2e8f0;
      padding-bottom: 12px;
    }

    .page-wrapper {
      max-width: 920px;
      margin: 0 auto;
    }

    h2 {
      margin-top: 0;
      font-size: 26px;
      text-align: center;
    }

    .btn-group {
      grid-column: span 2;
      display: flex;
      justify-content: flex-end;
      gap: 16px;
      margin-top: 20px;
      margin-bottom: 10px;
    }

    .btn-back,
    .btn-submit {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.2s ease;
    }

    .btn-back {
      background-color: #e0e6ed;
      color: #2c3e50;
    }

    .btn-back:hover {
      background-color: #d0dae3;
    }

    .btn-submit {
      background-color: #2ecc71;
      color: #fff;
    }

    .btn-submit:hover {
      background-color: #27ae60;
    }

    form {
      margin-top: 30px;
    }

.form-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
}

.form-section {
  margin-bottom: 32px;
  border-bottom: 1px solid #e2e8f0;
  padding-bottom: 20px;
}

.form-section h3 {
  font-size: 18px;
  font-weight: 600;
  margin-bottom: 16px;
  color: #334155;
}

    label {
      font-weight: 600;
      margin-bottom: 6px;
      display: block;
    }

    input[type="text"],
    input[type="number"],
    input[type="date"],
    input[type="file"],
    select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      background-color: #fff;
    }

    @media (max-width: 768px) {
      .form-grid {
        grid-template-columns: 1fr;
      }

      .btn-group {
        justify-content: center;
      }
    }
  </style>
</head>

<body>
  <?php include 'sidebar.php'; ?>
  <div class="content">
    <div class="header-box">
      <div class="header-content">
        <div class="header-text">
          <h1 class="page-title">
            📁 Upload Sertifikat Pegawai
          </h1>
          <p class="page-subtitle">
            Kelola dan unggah sertifikat pelatihan pegawai dengan mudah dan rapi.
          </p>
        </div>
        <div class="clock-box">
          <div class="date" id="tanggal"></div>
          <div class="time" id="jam"></div>
        </div>
      </div>
    </div>
    <div class="container">
      <h2>➕ Tambah Sertifikat ke User</h2>
     <form action="../process/proses_upload_sertifikat.php" method="POST" enctype="multipart/form-data">
  <div class="form-section">
    <h3>👤 Data Pengguna</h3>
    <div class="form-grid">
      <div>
        <label>Nama Pengguna</label>
        <select name="user_id" required>
          <option value="">-- Pilih User --</option>
          <?php while ($u = mysqli_fetch_assoc($users)): ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['nama']) ?></option>
          <?php endwhile; ?>
        </select>
      </div>

          <div>
        <label>NIP</label>
        <input type="number" name="nip" placeholder="Masukkan NIP..." required>        
      </div>

      <div>
        <label>Jabatan</label>
        <input type="text" name="jabatan" placeholder="Masukkan jabatan..." required>        
      </div>
    </div>
  </div>

<div class="form-section">
  <h3>📘 Informasi Diklat</h3>
  <div class="form-grid" style="grid-template-columns: repeat(4, 1fr); gap: 20px 24px;">
    <div>
      <label>Jenis Diklat</label>
      <select name="jenis_diklat" required>
        <option value="">-- Pilih Jenis Diklat --</option>
        <option>Diklat Struktural</option>
        <option>Diklat Fungsional</option>
        <option>Diklat Teknis</option>
        <option>Workshop</option>
        <option>Pelatihan Manajerial</option>
        <option>Pelatihan Sosial Kultural</option>
        <option>Sosialisasi</option>
        <option>Bimbingan Teknis</option>
        <option>Seminar</option>
        <option>Magang</option>
        <option>Kursus</option>
        <option>Penataran</option>
        <option>Pengembangan Kompetensi Dalam Bentuk Pelatihan Klasikal Lainnya</option>
        <option>Coaching</option>
        <option>Mentoring</option>
        <option>E-learning</option>
        <option>Pelatihan Jarak Jauh</option>
        <option>Detasering (Secondment)</option>
        <option>Pembelajaran Alam Terbuka (Outbond)</option>
        <option>Patok Banding (Benchmarking)</option>
        <option>Pertukaran Antara PNS Dengan Pegawai Swasta/BUMN/BUMD</option>
        <option>Belajar Mandiri (Self Development)</option>
        <option>Komunitas Belajar (Community of Practices)</option>
        <option>Bimbingan di Tempat Kerja</option>
        <option>Pengembangan Kompetensi Dalam Bentuk Pelatihan Nonklasikal Lainnya</option>
      </select>
    </div>

    <div>
      <label>Jenis Diklat Struktural</label>
      <select name="jenis_diklat_struktural">
        <option value="">-- Pilih Jenis Diklat Struktural --</option>
        <option>SEPADA</option>
        <option>SEPALA/ADUM/DIKLAT PIM TK. IV</option>
        <option>SEPADYA/SPAMA/DIKLAT PIM TK. III</option>
        <option>SPAMEN/SESPA/SESPANAS/DIKLAT PIM TK.II</option>
        <option>SEPATI/DIKLAT PIM TK. I</option>
        <option>SESPIM</option>
        <option>SESPATI</option>
        <option>Diklat Struktural Lainnya</option>
      </select>
    </div>

    <div>
      <label>Nama Diklat</label>
      <input type="text" name="nama_diklat" placeholder="Masukkan nama diklat..." required>
    </div>

    <div>
      <label>Institusi Penyelenggara</label>
       <input type="text" name="instansi" placeholder="Masukkan institusi penyelenggara..." required>
    </div>

    <div>
      <label>No Sertifikat</label>
       <input type="text" name="no_sertifikat" placeholder="Masukkan nomor sertifikat..." required>
    </div>

    <div>
      <label>Tanggal Mulai</label>
      <input type="date" name="tgl_mulai" required>
    </div>

    <div>
      <label>Tanggal Selesai</label>
      <input type="date" name="tgl_selesai" required>
    </div>

    <div>
      <label>Durasi Jam</label>
      <input type="number" name="durasi_jam" placeholder="Durasi dalam jam..." required>
    </div>
  </div>
</div>


  <div class="form-section">
    <h3>📎 Upload File</h3>
    <div class="form-grid">
      <div>
        <label>Upload Sertifikat</label>
        <input type="file" name="file_sertifikat" accept=".pdf,.jpg,.jpeg,.png" required>
      </div>
    </div>
  </div>

  <div class="btn-group">
    <a href="upload_sertifikat.php" class="btn-back">← Kembali</a>
    <button type="submit" class="btn-submit" name="simpan">Simpan</button>
  </div>
</form>

    </div>
  </div>
</body>

</html>