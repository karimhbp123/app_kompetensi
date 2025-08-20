<?php
session_start();
include '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id       = $_POST['user_id'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $nip = mysqli_real_escape_string($koneksi, $_POST['nip']);
    $jenis_diklat = mysqli_real_escape_string($koneksi, $_POST['jenis_diklat']);
    $jenis_diklat_struktural = mysqli_real_escape_string($koneksi, $_POST['jenis_diklat_struktural']);
    $jabatan = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
    $nama_diklat   = mysqli_real_escape_string($koneksi, $_POST['nama_diklat']);
    $instansi      = mysqli_real_escape_string($koneksi, $_POST['instansi']);
    $no_sertifikat = mysqli_real_escape_string($koneksi, $_POST['no_sertifikat']);
    $tgl_mulai     = $_POST['tgl_mulai'];
    $tgl_selesai   = $_POST['tgl_selesai'];
    $durasi_jam    = $_POST['durasi_jam'];

    $getUser = mysqli_query($koneksi, "SELECT nama FROM users WHERE id = $user_id");
    $user = mysqli_fetch_assoc($getUser);
    $nama = $user['nama'];

    $nama_user = strtolower($nama);
    $nama_user = preg_replace('/[^a-z0-9]+/', '_', $nama_user);
    $target_dir = __DIR__ . "/../sertifikat/" . $nama_user . "/";

    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    $file = $_FILES['file_sertifikat'];
    $file_name = basename($file['name']);
    $target_file = $target_dir . $file_name;
    $relative_file_path = $file_name;

    $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png'];
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) {
        echo "<script>alert('File harus PDF/JPG/PNG'); window.location.href='add_sertifikat.php';</script>";
        exit;
    }

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
            header("Location: ../admin/upload_sertifikat.php");
            exit;
        } else {
            echo "Gagal menyimpan ke database: " . mysqli_error($koneksi);
        }
    } else {
        echo "Gagal upload file sertifikat.";
    }
}
