<?php
session_start();
include '../config/db.php';

if (!isset($_POST['update'])) {
  header("Location: ../user/upload_sertifikat_user.php");
  exit;
}

$id = $_POST['id'];
$nama = $_POST['nama'];
$nip = $_POST['nip'];
$jenis_diklat = $_POST['jenis_diklat'];
$jenis_diklat_struktural = $_POST['jenis_diklat_struktural'];
$jabatan = $_POST['jabatan'];
$nama_diklat = $_POST['nama_diklat'];
$instansi = $_POST['instansi'];
$no_sertifikat = $_POST['no_sertifikat'];
$tgl_mulai = $_POST['tgl_mulai'];
$tgl_selesai = $_POST['tgl_selesai'];
$durasi_jam = $_POST['durasi_jam'];

$query = mysqli_query($koneksi, "SELECT file_sertifikat FROM diklat WHERE id=$id");
$oldData = mysqli_fetch_assoc($query);
$oldFile = $oldData['file_sertifikat'];

// Cek jika user upload file baru
if ($_FILES['file_sertifikat']['name']) {
  $file = $_FILES['file_sertifikat'];
  $namaFileBaru = uniqid() . "_" . basename($file['name']);

  // Buat folder berdasarkan nama (atau bisa juga dari database user_id)
  $user_folder = '../sertifikat/' . preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($nama)) . '/';
  if (!file_exists($user_folder)) {
    mkdir($user_folder, 0755, true);
  }

  $targetFile = $user_folder . $namaFileBaru;

  // Cek ekstensi file
  $ext = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
  $allowed = ['pdf', 'jpg', 'jpeg', 'png'];
  if (!in_array($ext, $allowed)) {
    echo "Ekstensi file tidak diizinkan.";
    exit;
  }

  // Upload file
  if (move_uploaded_file($file['tmp_name'], $targetFile)) {
    // Hapus file lama
    $oldFilePath = $user_folder . $oldFile;
    if ($oldFile && file_exists($oldFilePath)) {
      unlink($oldFilePath);
    }


    // Update dengan file baru
    $update = mysqli_query($koneksi, "UPDATE diklat SET
      nama='$nama',
      nip='$nip',
      jenis_diklat='$jenis_diklat',
      jenis_diklat_struktural='$jenis_diklat_struktural',
      jabatan='$jabatan',
      nama_diklat='$nama_diklat',
      instansi='$instansi',
      no_sertifikat='$no_sertifikat',
      tgl_mulai='$tgl_mulai',
      tgl_selesai='$tgl_selesai',
      durasi_jam='$durasi_jam',
      file_sertifikat='$namaFileBaru'
      WHERE id=$id
    ");
  } else {
    echo "Gagal mengupload file.";
    exit;
  }
} else {
  // Tanpa update file
  $update = mysqli_query($koneksi, "UPDATE diklat SET
    nama='$nama',
    nip='$nip',
    jenis_diklat='$jenis_diklat',
    jenis_diklat_struktural='$jenis_diklat_struktural',
    jabatan='$jabatan',
    nama_diklat='$nama_diklat',
    instansi='$instansi',
    no_sertifikat='$no_sertifikat',
    tgl_mulai='$tgl_mulai',
    tgl_selesai='$tgl_selesai',
    durasi_jam='$durasi_jam'
    WHERE id=$id
  ");
}

if ($update) {
  header("Location: ../user/upload_sertifikat_user.php?status=success");
} else {
  echo "Gagal update data.";
}
