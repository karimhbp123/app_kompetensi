<?php
$servername = "127.0.0.1";
$username   = "root";
$password   = "";
$port       = 3307;

// Koneksi utama (kompetensi_db)
$dbname = "kompetensi_db";
$koneksi = new mysqli($servername, $username, $password, $dbname, $port);
if ($koneksi->connect_error) {
    die("Koneksi ke $dbname gagal: " . $koneksi->connect_error);
}

// Koneksi tambahan (pegawai_db)
$dbname2 = "pegawai_db";
$koneksi2 = new mysqli($servername, $username, $password, $dbname2, $port);
if ($koneksi2->connect_error) {
    die("Koneksi ke $dbname2 gagal: " . $koneksi2->connect_error);
}
?>
