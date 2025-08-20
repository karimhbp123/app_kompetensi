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

$nama_session = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower(trim($_SESSION['nama'])));
$id = intval($_GET['id']);
$user_id = $_SESSION['user_id'];

// Ambil nama file sertifikat
$query = "SELECT nama, file_sertifikat FROM diklat WHERE id = $id AND user_id = $user_id";
$result = mysqli_query($koneksi, $query);

if (!$result || mysqli_num_rows($result) === 0) {
  // Data tidak ditemukan atau bukan milik user ini
  header("Location: ../user/upload_sertifikat_user.php");
  exit;
}

$data = mysqli_fetch_assoc($result);
$file = $data['file_sertifikat'];
$nama_folder = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower(trim($data['nama'])));

// Path file
$filePath = "../sertifikat/$nama_folder/$file";

if (!empty($file) && file_exists($filePath)) {
  if (unlink($filePath)) {
    // Jika file berhasil dihapus, cek apakah folder user kosong
    $folderPath = "../sertifikat/$nama_folder";
    if (is_dir($folderPath) && count(scandir($folderPath)) == 2) {
      // Folder kosong (hanya . dan ..)
      if (!rmdir($folderPath)) {
        error_log("❌ Gagal menghapus folder: $folderPath");
      } else {
        error_log("✅ Folder kosong berhasil dihapus: $folderPath");
      }
    }
  } else {
    error_log("❌ Gagal menghapus file: $filePath");
  }
} else {
  error_log("❌ File tidak ditemukan di path: $filePath");
}

// Hapus data dari database
$delete = mysqli_query($koneksi, "DELETE FROM diklat WHERE id = $id AND user_id = $user_id");

if ($delete) {
  header("Location: ../user/upload_sertifikat_user.php?status=deleted");
  exit;
} else {
  echo "❌ Gagal menghapus data dari database.";
}
