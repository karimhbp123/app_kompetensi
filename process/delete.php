<?php
session_start();
include '../config/db.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
  header("Location: ../auth/login.php");
  exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  header("Location: ../user/upload_sertifikat_user.php");
  exit;
}

$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Ambil nama file sertifikat
$query = "SELECT nama, file_sertifikat FROM diklat WHERE id = $id AND user_id = $user_id";
$result = mysqli_query($koneksi, $query);

if (!$result || mysqli_num_rows($result) === 0) {
  header("Location: ../user/upload_sertifikat_user.php");
  exit;
}

$data = mysqli_fetch_assoc($result);
$file = $data['file_sertifikat'];
$nama_folder = preg_replace('/[^a-z0-9]/', '_', strtolower(trim($data['nama'])));

// Path file
$filePath = "../sertifikat/$nama_folder/$file";

if (!empty($file) && file_exists($filePath)) {
  // Cek keamanan path
  if (strpos(realpath($filePath), realpath("../sertifikat/")) === 0) {
    if (unlink($filePath)) {
      // Hapus folder jika kosong
      $folderPath = "../sertifikat/$nama_folder";
      if (is_dir($folderPath) && count(scandir($folderPath)) == 2) {
        @rmdir($folderPath);
      }
    } else {
      error_log("❌ Gagal menghapus file: $filePath");
    }
  } else {
    error_log("❌ Path ilegal dicegah: $filePath");
    header("Location: ../user/upload_sertifikat_user.php?status=invalidpath");
    exit;
  }
} else {
  error_log("❌ File tidak ditemukan: $filePath");
}

// Hapus data dari database (prepared statement lebih aman)
$stmt = $koneksi->prepare("DELETE FROM diklat WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
if ($stmt->execute()) {
  header("Location: ../user/upload_sertifikat_user.php?status=deleted");
  exit;
} else {
  echo "❌ Gagal menghapus data dari database.";
}
