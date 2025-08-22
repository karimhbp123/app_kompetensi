<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id   = intval($_POST['user_id']);
    $nip       = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $jenis_diklat = mysqli_real_escape_string($koneksi, $_POST['jenis_diklat']);
    $jenis_diklat_struktural = mysqli_real_escape_string($koneksi, $_POST['jenis_diklat_struktural']);
    $jabatan   = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    $nama_diklat = mysqli_real_escape_string($koneksi, $_POST['nama_diklat']);
    $instansi  = mysqli_real_escape_string($koneksi, $_POST['instansi']);
    $no_sertifikat = mysqli_real_escape_string($koneksi, $_POST['no_sertifikat']);
    $tgl_mulai   = $_POST['tgl_mulai'];
    $tgl_selesai = $_POST['tgl_selesai'];
    $durasi_jam  = intval($_POST['durasi_jam']);

    // Ambil nama user dari tabel users
    $getUser = mysqli_query($koneksi, "SELECT nama FROM users WHERE id = $user_id");
    $user = mysqli_fetch_assoc($getUser);

    if (!$user) {
        echo "<script>alert('User tidak ditemukan!'); window.location.href='add_sertifikat.php';</script>";
        exit;
    }

    $nama = $user['nama'];

    // Normalisasi nama user → folder
    $nama_user = strtolower(trim($nama));
    $nama_user = preg_replace('/[^a-z0-9]+/', '_', $nama_user);

    $target_dir = __DIR__ . "/../sertifikat/" . $nama_user . "/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // File upload
    $file = $_FILES['file_sertifikat'];
    $original_name = basename($file['name']);
    $ext = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

    // Validasi ekstensi
    $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array($ext, $allowed_ext)) {
        echo "<script>alert('File harus PDF/JPG/PNG'); window.location.href='add_sertifikat.php';</script>";
        exit;
    }

    // Buat nama file unik & rapi
    $timestamp = date('YmdHis');
    $clean_diklat = preg_replace('/[^a-z0-9]+/', '_', strtolower($nama_diklat));
    $file_name = $user_id . "_" . $clean_diklat . "_" . $timestamp . "." . $ext;

    $target_file = $target_dir . $file_name;
    $relative_file_path = $file_name; // hanya simpan nama file di DB

    // Upload file
    if (move_uploaded_file($file['tmp_name'], $target_file)) {
        $created_by = 'admin';

        $query = "INSERT INTO diklat (
            user_id, nama, nip, jenis_diklat, jenis_diklat_struktural, jabatan, 
            nama_diklat, instansi, no_sertifikat, tgl_mulai, tgl_selesai, durasi_jam, 
            file_sertifikat, created_by
          ) VALUES (
            '$user_id', '$nama', '$nip', '$jenis_diklat', '$jenis_diklat_struktural', '$jabatan', 
            '$nama_diklat', '$instansi', '$no_sertifikat', '$tgl_mulai', '$tgl_selesai', '$durasi_jam', 
            '$relative_file_path', '$created_by'
          )";

        if (mysqli_query($koneksi, $query)) {
            header("Location: ../admin/upload_sertifikat.php?status=success");
            exit;
        } else {
            echo "❌ Gagal menyimpan ke database: " . mysqli_error($koneksi);
        }
    } else {
        echo "❌ Gagal upload file sertifikat.";
    }
}
