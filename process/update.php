<?php
session_start();
include '../config/db.php';

if (!isset($_POST['update'])) {
    header("Location: ../user/upload_sertifikat_user.php");
    exit;
}

$id                     = intval($_POST['id']);
$nama                   = mysqli_real_escape_string($koneksi, $_POST['nama']);
$nip                    = mysqli_real_escape_string($koneksi, $_POST['nip']);
$jenis_diklat           = mysqli_real_escape_string($koneksi, $_POST['jenis_diklat']);
$jenis_diklat_struktural= mysqli_real_escape_string($koneksi, $_POST['jenis_diklat_struktural']);
$jabatan                = mysqli_real_escape_string($koneksi, $_POST['jabatan']);
$nama_diklat            = mysqli_real_escape_string($koneksi, $_POST['nama_diklat']);
$instansi               = mysqli_real_escape_string($koneksi, $_POST['instansi']);
$no_sertifikat          = mysqli_real_escape_string($koneksi, $_POST['no_sertifikat']);
$tgl_mulai              = mysqli_real_escape_string($koneksi, $_POST['tgl_mulai']);
$tgl_selesai            = mysqli_real_escape_string($koneksi, $_POST['tgl_selesai']);
$durasi_jam             = intval($_POST['durasi_jam']);

// Ambil data lama
$query    = mysqli_query($koneksi, "SELECT file_sertifikat FROM diklat WHERE id=$id");
$oldData  = mysqli_fetch_assoc($query);
$oldFile  = $oldData['file_sertifikat'] ?? null;

// Cek jika ada file baru diupload
if (!empty($_FILES['file_sertifikat']['name'])) {
    $file         = $_FILES['file_sertifikat'];
    $namaFileBaru = uniqid() . "_" . basename($file['name']);

    // Buat folder berdasarkan nama user
    $user_folder = '../sertifikat/' . preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($nama)) . '/';
    if (!is_dir($user_folder)) {
        mkdir($user_folder, 0755, true);
    }

    $targetFile = $user_folder . $namaFileBaru;

    // Validasi ekstensi
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed  = ['pdf', 'jpg', 'jpeg', 'png'];
    if (!in_array($ext, $allowed)) {
        echo "<script>alert('Ekstensi file tidak diizinkan. Hanya PDF/JPG/PNG'); window.location.href='../user/upload_sertifikat_user.php';</script>";
        exit;
    }

    // Upload file baru
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        // Hapus file lama jika ada
        if ($oldFile && file_exists($user_folder . $oldFile)) {
            unlink($user_folder . $oldFile);
        }

        // Update dengan file baru
        $sql = "UPDATE diklat SET
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
            WHERE id=$id";
    } else {
        echo "<script>alert('Gagal mengupload file baru.'); window.location.href='../user/upload_sertifikat_user.php';</script>";
        exit;
    }
} else {
    // Update tanpa ganti file
    $sql = "UPDATE diklat SET
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
        WHERE id=$id";
}

$update = mysqli_query($koneksi, $sql);

if ($update) {
    header("Location: ../user/upload_sertifikat_user.php?status=success");
    exit;
} else {
    echo "Gagal update data: " . mysqli_error($koneksi);
}
