<?php
ob_start();
require '../vendor/autoload.php';
include '../config/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\MemoryDrawing;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

session_start();
if (!isset($_SESSION['user_id'])) die("Akses ditolak");

$user_id = $_SESSION['user_id'];
$username = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'user';
$query = mysqli_query($koneksi, "SELECT * FROM diklat WHERE user_id = $user_id");

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Header kolom
$headers = ['No', 'Nama', 'NIP', 'Jenis Diklat', 'Jenis Diklat Struktural', 'Jabatan', 'Nama Diklat', 'Instusi Penyelenggara', 'No Sertifikat', 'Tanggal Mulai', 'Tanggal Selesai', 'Durasi (jam)', 'Sertifikat'];
$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '1', $header);
    $col++;
}

$rowNum = 2;
$no = 1;
while ($row = mysqli_fetch_assoc($query)) {
    $sheet->setCellValue("A$rowNum", $no++);
    $sheet->setCellValue("B$rowNum", $row['nama']);
    $sheet->setCellValue("C$rowNum", $row['nip']);
    $sheet->setCellValue("D$rowNum", $row['jenis_diklat']);
    $sheet->setCellValue("E$rowNum", $row['jenis_diklat_struktural']);
    $sheet->setCellValue("F$rowNum", $row['jabatan']);
    $sheet->setCellValue("G$rowNum", $row['nama_diklat']);
    $sheet->setCellValue("H$rowNum", $row['instansi']);
    $sheet->setCellValue("I$rowNum", $row['no_sertifikat']);
    $sheet->setCellValue("J$rowNum", date('d-m-Y', strtotime($row['tgl_mulai'])));
    $sheet->setCellValue("K$rowNum", date('d-m-Y', strtotime($row['tgl_selesai'])));
    $sheet->setCellValue("L$rowNum", $row['durasi_jam']);
    // Ganti bagian ini dalam loop saat menulis data Excel
    $sertifikatPath = '../sertifikat/' . $row['file_sertifikat']; // ✅ Ganti dari 'sertifikat' ke 'file_sertifikat'

    if (!empty($row['file_sertifikat']) && file_exists($sertifikatPath)) {
        $imageType = @exif_imagetype($sertifikatPath);
        if (in_array($imageType, [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF])) {
            $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
            $drawing->setPath($sertifikatPath);
            $drawing->setCoordinates("L$rowNum");
            $drawing->setHeight(70); // Ukuran gambar
            $drawing->setOffsetX(10);
            $drawing->setOffsetY(5);
            $drawing->setWorksheet($sheet);
            $sheet->getRowDimension($rowNum)->setRowHeight(80); // Pastikan baris cukup tinggi
        } else {
            $sheet->setCellValue("L$rowNum", 'Format tidak didukung');
        }
    } else {
        $sheet->setCellValue("L$rowNum", 'File tidak ditemukan');
    }

    $rowNum++;
}

// Styling
$lastRow = $rowNum - 1;
$sheet->getStyle("A1:M$lastRow")->applyFromArray([
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER,
        'wrapText' => true,
    ],
]);

foreach (range('A', 'M') as $col) {
    $sheet->getColumnDimension($col)->setAutoSize(true);
}

ob_end_clean();

$filename = 'data_diklat_' . strtolower(str_replace(' ', '_', $username)) . '.xlsx';
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
