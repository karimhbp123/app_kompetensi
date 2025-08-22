<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../user/dashboard.php");
    exit;
}

include '../config/db.php';

// Ambil role dari parameter GET
$role = isset($_GET['role']) ? $_GET['role'] : '';
if ($role !== 'asn' && $role !== 'nonasn') {
    echo "Parameter 'role' tidak valid.";
    exit;
}

// Query data
$query = mysqli_query($koneksi, "
  SELECT d.*, u.nama, u.nip 
  FROM diklat d
  JOIN users u ON d.user_id = u.id
  WHERE u.role = '$role'
  ORDER BY d.tgl_mulai DESC
");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Data Diklat <?= strtoupper($role) ?></title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        .export-btn {
            margin-bottom: 15px;
            padding: 8px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .export-btn:hover {
            background-color: #0056b3;
        }

        .sertifikat-preview {
            max-width: 100px;
            max-height: 80px;
        }
    </style>
</head>

<body>

    <h2>Data Diklat (<?= strtoupper($role) ?>)</h2>

    <!-- Tombol Export Excel Lengkap -->
    <button class="export-btn" onclick="window.location.href='export_diklat.php?role=<?= $role ?>'">
        Export Lengkap (Excel + Sertifikat)
    </button>

    <table id="dataTable" class="display nowrap" style="width:100%">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>NIP</th>
                <th>Jenis Diklat</th>
                <th>Jenis Struktural</th>
                <th>Jabatan</th>
                <th>Nama Diklat</th>
                <th>Instansi</th>
                <th>No Sertifikat</th>
                <th>Tgl Mulai</th>
                <th>Tgl Selesai</th>
                <th>Durasi (jam)</th>
                <th>Sertifikat</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($row = mysqli_fetch_assoc($query)) {
                // buat nama folder dari nama user
                $namaFolder = preg_replace('/[^A-Za-z0-9_\-]/', '_', $row['nama']);
                $userFolder = '../sertifikat/' . $namaFolder . '/';

                $sertifikatCell = "File tidak ditemukan";

                if (!empty($row['file_sertifikat'])) {
                    $sertifikatPath = $userFolder . $row['file_sertifikat'];

                    if (file_exists($sertifikatPath)) {
                        $ext = strtolower(pathinfo($sertifikatPath, PATHINFO_EXTENSION));
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                            $sertifikatCell = "<img src='{$sertifikatPath}' class='sertifikat-preview'>";
                        } elseif ($ext === 'pdf') {
                            $sertifikatCell = "<a href='{$sertifikatPath}' target='_blank'>Lihat PDF</a>";
                        }
                    }
                }

                echo "<tr>
            <td>{$no}</td>
            <td>{$row['nama']}</td>
            <td>{$row['nip']}</td>
            <td>{$row['jenis_diklat']}</td>
            <td>{$row['jenis_diklat_struktural']}</td>
            <td>{$row['jabatan']}</td>
            <td>{$row['nama_diklat']}</td>
            <td>{$row['instansi']}</td>
            <td>{$row['no_sertifikat']}</td>
            <td>" . date('d-m-Y', strtotime($row['tgl_mulai'])) . "</td>
            <td>" . date('d-m-Y', strtotime($row['tgl_selesai'])) . "</td>
            <td>{$row['durasi_jam']}</td>
            <td>{$sertifikatCell}</td>
          </tr>";
                $no++;
            }

            ?>
        </tbody>
    </table>

    <!-- jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <!-- Buttons & Export (pakai JSZip & pdfmake) -->
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                responsive: true,
                scrollX: true,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'csvHtml5',
                        title: 'Data <?= strtoupper($role) ?>'
                    },
                    {
                        extend: 'pdfHtml5',
                        title: 'Data <?= strtoupper($role) ?>',
                        orientation: 'landscape',
                        pageSize: 'A4'
                    }
                ],
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data tersedia",
                    infoFiltered: "(disaring dari _MAX_ total data)"
                }
            });
        });
    </script>

</body>

</html>