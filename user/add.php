<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id'])) header("Location: ../auth/login.php");
?>
<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>➕ Tambah Data Kompetensi</title>
  <link rel="icon" type="image/png" href="/app_kompetensi/favicon.png">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    html,
    body {
      margin: 0;
      padding: 0;
      background-color: #f3f4f6;
      height: 100%;
      overflow-x: hidden;
    }

    .container {
      max-width: 920px;
      margin: 40px auto;
      background-color: #ffffff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
      overflow: auto;
      /* Tambahan ini */
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
      grid-template-columns: 1fr 1fr;
      gap: 20px 40px;
      margin-top: 20px;
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
  <div class="container">
    <h2>➕ Tambah Data Kompetensi</h2>
    <form action="../process/add.php" method="POST" enctype="multipart/form-data">
      <div class="form-grid">
        <label>Nama</label>
        <input type="text" name="nama" required>

        <label>NIP</label>
        <input type="text" name="nip" required>

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


        <label>Jabatan</label>
        <input type="text" name="jabatan" required>

        <label>Nama Diklat</label>
        <input type="text" name="nama_diklat" required>

        <label>Institusi Penyelenggara</label>
        <input type="text" name="instansi" required>

        <label>No Sertifikat</label>
        <input type="text" name="no_sertifikat" required>

        <label>Tanggal Mulai:</label>
        <input type="date" name="tanggal_mulai" required>

        <label>Tanggal Selesai:</label>
        <input type="date" name="tanggal_selesai" required>

        <label>Durasi Jam</label>
        <input type="number" name="durasi_jam" required>

        <label>Upload Sertifikat</label>
        <input type="file" name="file_sertifikat" accept=".pdf,.jpg,.jpeg,.png" required>

        <div class="btn-group">
          <a href="data_diklat.php" class="btn-back">← Kembali</a>
          <button type="submit" class="btn-submit" name="simpan">Simpan</button>
        </div>
      </div>
    </form>

  </div>
</body>

</html>