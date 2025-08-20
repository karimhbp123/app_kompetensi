<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id'])) header("Location: ../auth/login.php");

// Ambil ID diklat dari URL
$id = $_GET['id'];
$query = mysqli_query($koneksi, "SELECT * FROM diklat WHERE id=$id");
$data = mysqli_fetch_assoc($query);

// Cek jika data tidak ditemukan
if (!$data) {
  echo "Data tidak ditemukan";
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>✏️ Edit Data Kompetensi</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Inter', sans-serif;
    }

    body {
      margin: 0;
      padding: 0;
      background-color: #f3f4f6;
    }

    .container {
      max-width: 920px;
      margin: 50px auto;
      background-color: #ffffff;
      padding: 30px 40px;
      border-radius: 12px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    h2 {
      font-size: 26px;
      text-align: center;
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

    .btn-group {
      grid-column: span 2;
      display: flex;
      justify-content: flex-end;
      gap: 16px;
      margin-top: 20px;
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
    }

    .btn-back {
      background-color: #e0e6ed;
      color: #2c3e50;
    }

    .btn-back:hover {
      background-color: #d0dae3;
    }

    .btn-submit {
      background-color: #3498db;
      color: #fff;
    }

    .btn-submit:hover {
      background-color: #2980b9;
    }

    .preview {
      grid-column: span 2;
    }

    .preview img,
    .preview a {
      max-width: 200px;
      display: block;
      margin-top: 8px;
    }
  </style>
</head>

<body>
  <div class="container">
    <h2>✏️ Edit Data Kompetensi</h2>
    <form action="../process/update.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="id" value="<?= $data['id'] ?>">
      <div class="form-grid">
        <label>Nama</label>
        <input type="text" name="nama" value="<?= $data['nama'] ?>" required>

        <label>NIP</label>
        <input type="text" name="nip" value="<?= $data['nip'] ?>" required>

        <label>Jenis Diklat</label>
        <select name="jenis_diklat" required>
          <option value="">-- Pilih Jenis Diklat --</option>
          <?php
          $jenisDiklatOptions =   [
            "Diklat Struktural",
            "Diklat Fungsional",
            "Diklat Teknis",
            "Workshop",
            "Pelatihan Manajerial",
            "Pelatihan Sosial Kultural",
            "Sosialisasi",
            "Bimbingan Teknis",
            "Seminar",
            "Magang",
            "Kursus",
            "Penataran",
            "Pengembangan Kompetensi Dalam Bentuk Pelatihan Klasikal Lainnya",
            "Coaching",
            "Mentoring",
            "E-learning",
            "Pelatihan Jarak Jauh",
            "Detasering (Secondment)",
            "Pembelajaran Alam Terbuka (Outbond)",
            "Patok Banding (Benchmarking)",
            "Pertukaran Antara PNS Dengan Pegawai Swasta/BUMN/BUMD",
            "Belajar Mandiri (Self Development)",
            "Komunitas Belajar (Community of Practices)",
            "Bimbingan di tempat kerja",
            "Pengembangan Kompetensi Dalam Bentuk Pelatihan Nonklasikal Lainnya"
          ];
          foreach ($jenisDiklatOptions as $opt) {
            $selected = ($data['jenis_diklat'] == $opt) ? "selected" : "";
            echo "<option value=\"$opt\" $selected>$opt</option>";
          }
          ?>
        </select>
        <label>Jenis Diklat Struktural</label>
        <select name="jenis_diklat_struktural">
          <option value="">-- Pilih Jenis Struktural --</option>
          <?php
          $jenisStrukturalOptions = [
            "SEPADA",
            "SEPALA/ADUM/DIKLAT PIM TK. IV",
            "SEPADYA/SPAMA/DIKLAT PIM TK. III",
            "SPAMEN/SESPA/SESPANAS/DIKLAT PIM TK.II",
            "SEPATI/DIKLAT PIM TK. I",
            "SESPIM",
            "SESPATI",
            "Diklat Struktural Lainnya"
          ];
          foreach ($jenisStrukturalOptions as $opt) {
            $selected = (isset($data['jenis_diklat_struktural']) && $data['jenis_diklat_struktural'] == $opt) ? "selected" : "";
            echo "<option value=\"$opt\" $selected>$opt</option>";
          }
          ?>
        </select>


        <label>Jabatan</label>
        <input type="text" name="jabatan" value="<?= $data['jabatan'] ?>" required>

        <label>Nama Diklat</label>
        <input type="text" name="nama_diklat" value="<?= $data['nama_diklat'] ?>" required>

        <label>Institusi Penyelenggara</label>
        <input type="text" name="instansi" value="<?= $data['instansi'] ?>" required>

        <label>No Sertifikat</label>
        <input type="text" name="no_sertifikat" value="<?= $data['no_sertifikat'] ?>" required>

        <label>Tanggal Mulai</label>
        <input type="date" name="tgl_mulai" value="<?= !empty($data['tgl_mulai']) ? date('Y-m-d', strtotime($data['tgl_mulai'])) : '' ?>" required>

        <label>Tanggal Selesai</label>
        <input type="date" name="tgl_selesai" value="<?= !empty($data['tgl_selesai']) ? date('Y-m-d', strtotime($data['tgl_selesai'])) : '' ?>" required>

        <label>Durasi Jam</label>
        <input type="number" name="durasi_jam" value="<?= $data['durasi_jam'] ?>" required>

        <label>Ganti Sertifikat (jika perlu)</label>
        <input type="file" name="file_sertifikat" accept=".pdf,.jpg,.jpeg,.png">

        <div class="preview">
          <label>File Sertifikat Saat Ini:</label>
          <?php
          $filename = isset($data['file_sertifikat']) ? $data['file_sertifikat'] : '';
          $nama_folder = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower(trim($data['nama'])));
          $filepath = "../sertifikat/$nama_folder/$filename";
          if (!empty($filename) && file_exists($filepath)) {
            $ext = pathinfo($filepath, PATHINFO_EXTENSION);
            if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png'])) {
              echo "<img src='$filepath' alt='Sertifikat'>";
            } else {
              echo "<a href='$filepath' target='_blank'>Lihat Sertifikat</a>";
            }
          } else {
            echo "<i>File tidak ditemukan</i>";
          }
          ?>
        </div>

        <div class="btn-group">
          <a href="dashboard.php" class="btn-back">← Kembali</a>
          <button type="submit" class="btn-submit" name="update">Simpan Perubahan</button>
        </div>
      </div>
    </form>
  </div>
</body>

</html>