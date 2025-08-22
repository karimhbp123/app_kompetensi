<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

if (isset($_POST['simpan'])) {
  $user_id = $_SESSION['user_id'];
  $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
  $nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
  $jenis_diklat = mysqli_real_escape_string($koneksi, $_POST['jenis_diklat']);
  $jenis_diklat_struktural = mysqli_real_escape_string($koneksi, $_POST['jenis_diklat_struktural']);
  $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
  $nama_diklat = mysqli_real_escape_string($koneksi, $_POST['nama_diklat']);
  $instansi = mysqli_real_escape_string($koneksi, $_POST['instansi']);
  $no_sertifikat = mysqli_real_escape_string($koneksi, $_POST['no_sertifikat']);
  $tgl_mulai = $_POST['tanggal_mulai'];
  $tgl_selesai = $_POST['tanggal_selesai'];
  $durasi_jam = (int) $_POST['durasi_jam'];

  // ======== HANDLE FILE UPLOAD =========
  $namaFileAsli = $_FILES['file_sertifikat']['name'];
  $tmp = $_FILES['file_sertifikat']['tmp_name'];

  // Bersihkan nama file → ganti spasi jadi underscore, buang karakter aneh
  $namaFileAman = preg_replace('/[^A-Za-z0-9_\-.]/', '_', $namaFileAsli);

  // Tambahkan uniqid supaya tidak bentrok
  $filename = uniqid() . "_" . $namaFileAman;

  // Nama folder user berdasarkan nama
  $folder_nama = strtolower(trim($nama)); 
  $folder_nama = preg_replace('/[^a-z0-9]/', '_', $folder_nama); 
  $user_folder = '../sertifikat/' . $folder_nama . '/';

  // Buat folder jika belum ada
  if (!file_exists($user_folder)) {
    mkdir($user_folder, 0755, true);
  }

  $path = $user_folder . $filename;

  if (move_uploaded_file($tmp, $path)) {
    $query = "
      INSERT INTO diklat (
        id, user_id, nama, nip, jenis_diklat, jenis_diklat_struktural, jabatan, nama_diklat,
        instansi, no_sertifikat, tgl_mulai, tgl_selesai, durasi_jam, file_sertifikat
      ) VALUES (
        '', '$user_id', '$nama', '$nip', '$jenis_diklat', '$jenis_diklat_struktural', '$jabatan', '$nama_diklat',
        '$instansi', '$no_sertifikat', '$tgl_mulai', '$tgl_selesai', '$durasi_jam', '$filename'
      )
    ";

    if (mysqli_query($koneksi, $query)) {
      header("Location: ../user/upload_sertifikat_user.php");
      exit;
    } else {
      echo "Gagal menyimpan ke database: " . mysqli_error($koneksi);
    }
  } else {
    echo "Gagal upload file sertifikat.";
  }
}
?>
