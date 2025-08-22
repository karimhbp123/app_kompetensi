<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user/dashboard.php");
    exit;
}

include '../config/db.php';
require __DIR__ . '/libs/SimpleXLSXGen.php';

use Shuchkin\SimpleXLSXGen;

// untuk hosting prod lokal harus ganti
$baseUrl = 'https://appskompetensi.rf.gd/sertifikat/'; // URL publik sertifikat

$role = isset($_GET['role']) ? $_GET['role'] : '';
if ($role !== 'asn' && $role !== 'nonasn') {
    echo "Parameter 'role' tidak valid.";
    exit;
}

// Ambil data
$query = mysqli_query($koneksi, "
    SELECT d.*, u.nama, u.nip, u.id as user_id
    FROM diklat d
    JOIN users u ON d.user_id = u.id
    WHERE u.role = '$role'
    ORDER BY d.tgl_mulai DESC
");

$data = [];
$headers = [
    'No',
    'Nama',
    'NIP',
    'Jenis Diklat',
    'Jenis Diklat Struktural',
    'Jabatan',
    'Nama Diklat',
    'Instansi Penyelenggara',
    'No Sertifikat',
    'Tanggal Mulai',
    'Tanggal Selesai',
    'Durasi (Jam)',
    'Link Sertifikat'
];
$data[] = $headers;

$no = 1;
while ($row = mysqli_fetch_assoc($query)) {
    $fileName = trim($row['file_sertifikat']); // buang \r\n dan spasi
    $namaFolder = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower(trim($row['nama'])));

    if (!empty($fileName)) {
        $fileUrl = $baseUrl . $namaFolder . '/' . rawurlencode($fileName);
        $sertifikatVal = $fileUrl;
    } else {
        $sertifikatVal = 'File tidak ditemukan';
    }
    $data[] = [
        $no++,
        $row['nama'],
        $row['nip'],
        $row['jenis_diklat'],
        $row['jenis_diklat_struktural'],
        $row['jabatan'],
        $row['nama_diklat'],
        $row['instansi'],
        $row['no_sertifikat'],
        date('d-m-Y', strtotime($row['tgl_mulai'])),
        date('d-m-Y', strtotime($row['tgl_selesai'])),
        $row['durasi_jam'],
        $sertifikatVal
    ];
}

// Generate Excel
$xlsx = SimpleXLSXGen::fromArray($data);
$filename = 'Data_' . strtoupper($role) . '_' . date('Y-m-d') . '.xlsx';

// Header agar langsung download sekali klik
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$xlsx->saveAs('php://output');
exit;
