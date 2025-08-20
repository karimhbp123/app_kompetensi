<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$data = mysqli_query($koneksi, "SELECT d.*, u.nip, u.nama 
                                FROM diklat d
                                JOIN users u ON d.user_id = u.id
                                WHERE d.user_id = $user_id
                                ORDER BY d.id DESC");

date_default_timezone_set('Asia/Jakarta');
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Upload Sertifikat</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
        }

        .content {
            margin-left: 235px;
            padding: 40px 30px;
        }

        .header-box {
            background: #ffffff;
            padding: 28px 32px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
            margin-bottom: 32px;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .clock-box {
            text-align: right;
        }

        .clock-box .date {
            font-size: 14px;
            color: #475569;
            margin-bottom: 4px;
        }

        .clock-box .time {
            font-size: 20px;
            font-weight: bold;
            color: #1e40af;
            font-family: 'Courier New', Courier, monospace;
        }

        .welcome {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            animation: fadeInDown 0.5s ease;
        }

        .highlight {
            color: #2563eb;
        }

        .subtext {
            font-size: 14px;
            color: #475569;
            font-style: italic;
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .container {
            margin-left: 0;
            padding: 0;
            max-width: 100%;
        }


        th,
        td {
            white-space: nowrap;
        }

        .btn {
            padding: 10px 20px;
            font-weight: 600;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            border: none;
        }

        .btn.add {
            background-color: #10b981;
        }

        .btn.add:hover {
            background-color: #059669;
        }

        .btn.download {
            background-color: #f59e0b;
        }

        .btn.download:hover {
            background-color: #d97706;
        }

        .card-box {
            background: white;
            padding: 28px 32px;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
            overflow-x: auto;
            margin-top: 20px;
        }

        .action-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 16px;
        }

        .modern-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        .modern-table th,
        .modern-table td {
            padding: 14px 12px;
            border: 1px solid #cbd5e1;
            text-align: center;
            font-size: 10px;
            word-wrap: break-word;
        }

        .modern-table th {
            background-color: #e0e7ff;
            color: #1e3a8a;
            font-weight: 800;
            font-size: 11px;
        }

        .aksi a {
            display: inline-block;
            margin: 2px;
            padding: 6px 8px;
            font-size: 11px;
            border-radius: 4px;
            color: white;
            text-decoration: none;
        }

        .edit {
            background-color: #3b82f6;
        }

        .edit:hover {
            background-color: #2563eb;
        }

        .delete {
            background-color: #ef4444;
        }

        .delete:hover {
            background-color: #dc2626;
        }

        .badge-view {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 4px 10px;
            font-size: 12px;
            font-weight: 500;
            color: #ffffff;
            background-color: #3b82f6;
            border-radius: 999px;
            text-decoration: none;
            line-height: 1;
            box-shadow: 0 1px 3px rgba(59, 130, 246, 0.15);
            transition: all 0.2s ease-in-out;
        }

        .badge-view:hover {
            background-color: #2563eb;
            transform: scale(1.02);
            box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
        }

        #imgModal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.8);
            justify-content: center;
            align-items: center;
        }

        #imgModal iframe,
        #imgModal img {
            max-width: 90%;
            max-height: 90%;
            border: 2px solid white;
            border-radius: 10px;
            background: white;
        }

        #imgModal span {
            position: absolute;
            top: 10px;
            right: 20px;
            font-size: 40px;
            color: white;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <?php include 'sidebar_user.php'; ?>
    <div class="content">
        <div class="header-box">
            <div class="header-content">
                <div class="header-text">
                    <h2 class="welcome">👋<span class="highlight">Upload Sertifikat Saya</span></h2>
                    <p class="subtext">Selamat datang kembali di upload sertifikat saya. Yuk produktif hari ini 💪</p>
                </div>
                <div class="clock-box">
                    <div class="date" id="tanggal"></div>
                    <div class="time" id="jam"></div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="card-box">
                <div class="action-bar">
                    <a href="add.php" class="btn add">➕ Tambah Data</a>
                    <a href="export_excel.php" class="btn download">📥 Download Excel</a>
                </div>
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Aksi</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Jabatan</th>
                            <th>Jenis Diklat</th>
                            <th>Jenis Diklat Struktural</th>
                            <th>Nama Diklat</th>
                            <th>Institusi</th>
                            <th>No Sertifikat</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Durasi</th>
                            <th>Sertifikat</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1;
                        while ($row = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td class="aksi">
                                    <a href="edit.php?id=<?= $row['id'] ?>" class="edit">✏️</a>
                                    <a href="../process/delete.php?id=<?= $row['id'] ?>" class="delete" onclick="return confirm('Yakin ingin hapus?')">🗑️</a>
                                </td>
                                <td><?= htmlspecialchars($row['nama']) ?></td>
                                <td><?= htmlspecialchars($row['nip']) ?></td>
                                <td><?= htmlspecialchars($row['jabatan']) ?></td>
                                <td><?= htmlspecialchars($row['jenis_diklat']) ?></td>
                                <td><?= htmlspecialchars($row['jenis_diklat_struktural']) ?></td>
                                <td><?= htmlspecialchars($row['nama_diklat']) ?></td>
                                <td><?= htmlspecialchars($row['instansi']) ?></td>
                                <td><?= htmlspecialchars($row['no_sertifikat']) ?></td>
                                <td><?= date('d M Y', strtotime($row['tgl_mulai'])) ?></td>
                                <td><?= date('d M Y', strtotime($row['tgl_selesai'])) ?></td>
                                <td><?= $row['durasi_jam'] ?> jam</td>
                                <td>
                                    <?php if (!empty($row['file_sertifikat'])) : ?>
                                        <?php
                                        $nama_folder = preg_replace('/[^a-zA-Z0-9_-]/', '_', strtolower($row['nama']));
                                        $file_path = "../sertifikat/{$nama_folder}/{$row['file_sertifikat']}";
                                        $file_exists = file_exists($file_path);
                                        ?>
                                        <?php if ($file_exists) : ?>
                                            <a href="#"
                                                class="badge-view"
                                                data-img="<?= htmlspecialchars($file_path) ?>">
                                                <span class="icon">🔍</span>
                                                <span class="text">View</span>
                                            </a>
                                        <?php else : ?>
                                            <span style="color:red;">File tidak ditemukan</span>
                                        <?php endif; ?>

                                    <?php else : ?>
                                        <span style="color: #999;">Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="imgModal">
        <span onclick="closeModal()">&times;</span>
        <img id="modalImg" src="" style="display: none;">
        <iframe id="modalPdf" src="" width="80%" height="90%" style="border: none; display: none;"></iframe>
    </div>
    <script>
        function updateClock() {
            const now = new Date();
            const hari = now.toLocaleDateString('id-ID', {
                weekday: 'long'
            });
            const tanggal = now.toLocaleDateString('id-ID', {
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
            const jam = now.toLocaleTimeString('id-ID', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });

            document.getElementById("tanggal").textContent = `${hari}, ${tanggal}`;
            document.getElementById("jam").textContent = jam;
        }
        setInterval(updateClock, 1000);
        updateClock();
        const viewLinks = document.querySelectorAll('.badge-view');
        const modal = document.getElementById('imgModal');
        const modalImg = document.getElementById('modalImg');
        const modalPdf = document.getElementById('modalPdf');

        viewLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const filePath = this.getAttribute('data-img');
                const extension = filePath.split('.').pop().toLowerCase();

                modalImg.style.display = 'none';
                modalPdf.style.display = 'none';
                modalImg.src = '';
                modalPdf.src = '';

                if (extension === 'pdf') {
                    modalPdf.src = filePath;
                    modalPdf.style.display = 'block';
                } else {
                    modalImg.src = filePath;
                    modalImg.style.display = 'block';
                }

                modal.style.display = 'flex';
            });
        });

        function closeModal() {
            modal.style.display = 'none';
            modalImg.src = '';
            modalPdf.src = '';
            modalImg.style.display = 'none';
            modalPdf.style.display = 'none';
        }
    </script>

</body>

</html>