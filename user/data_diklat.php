<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

// ambil NIP user
$q_user = mysqli_query($koneksi, "SELECT nip, nama FROM users WHERE id = '$user_id'");
$user_data = mysqli_fetch_assoc($q_user);
$nip_user = $user_data['nip'];

// ambil data pegawai
$q_non_asn = mysqli_query($koneksi2, "SELECT * FROM pegawai_non_asn WHERE nip = '$nip_user'");
$pegawai_non_asn = mysqli_fetch_assoc($q_non_asn);

$q_asn = mysqli_query($koneksi2, "SELECT * FROM pegawai_asn WHERE nip = '$nip_user'");
$pegawai_asn = mysqli_fetch_assoc($q_asn);

if ($pegawai_non_asn) {
    $pegawai = $pegawai_non_asn;
    $jenis_pegawai = 'non_asn';
} elseif ($pegawai_asn) {
    $pegawai = $pegawai_asn;
    $jenis_pegawai = 'asn';
} else {
    $pegawai = null;
    $jenis_pegawai = null;
}

// ===================== HANDLE AJAX UPDATE =====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {

    $nama    = mysqli_real_escape_string($koneksi2, $_POST['nama']);
    $nip     = mysqli_real_escape_string($koneksi2, $_POST['nip']);
    $jabatan = mysqli_real_escape_string($koneksi2, $_POST['jabatan']);
    $ruang   = mysqli_real_escape_string($koneksi2, $_POST['ruang']);
    $foto_path = $pegawai['foto'] ?? null;

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $nama_folder = str_replace(' ', '_', $pegawai['nama']);
        $upload_dir = 'uploads/' . $nama_folder . '/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);

        $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9-_\.]/', '', $_FILES['foto']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
            if (!empty($foto_path) && file_exists($foto_path)) unlink($foto_path);
            $foto_path = $target_file;
        }
    }

    if ($jenis_pegawai === 'non_asn') {
        $rumpun_jabatan = mysqli_real_escape_string($koneksi2, $_POST['rumpun_jabatan']);
        $sql = "UPDATE pegawai_non_asn 
                SET nama='$nama', nip='$nip', jabatan='$jabatan', rumpun_jabatan='$rumpun_jabatan', ruang='$ruang', foto='$foto_path'
                WHERE nip='$nip'";
    } elseif ($jenis_pegawai === 'asn') {
        $bidang = mysqli_real_escape_string($koneksi2, $_POST['bidang']);
        $sql = "UPDATE pegawai_asn 
                SET nama='$nama', nip='$nip', jabatan='$jabatan', bidang='$bidang', ruang='$ruang', foto='$foto_path'
                WHERE nip='$nip'";
    }

    if (isset($sql) && mysqli_query($koneksi2, $sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($koneksi2)]);
    }
    exit;
}

// ===================== AMBIL DATA DIKLAT =====================
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
    <title>Data Diklat Saya</title>
    <link rel="icon" type="image/png" href="/app_kompetensi/favicon.png">
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

        /* Cardbox (tidak ubah ukuran) */
        .card-box {
            background: white;
            padding: 28px 32px;
            border-radius: 16px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.04);
            overflow-x: auto;
            margin-top: 20px;
        }

        /* --- Konten baru di dalam cardbox --- */
        .profile-wrapper {
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }

        .profile-photo {
            flex: 0 0 120px;
            text-align: center;
        }

        .profile-photo img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #e2e8f0;
        }

        .profile-info {
            flex: 1;
        }

        .info-group {
            margin-bottom: 15px;
        }

        .info-label {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 3px;
            font-weight: 600;
        }

        .info-value {
            font-size: 15px;
            color: #1e293b;
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #f8fafc;
        }

        .form-actions {
            text-align: right;
            margin-top: 20px;
        }

        .form-actions button {
            background: linear-gradient(90deg, #2563eb, #1d4ed8);
            color: white;
            padding: 10px 22px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        .form-actions button:hover {
            background: linear-gradient(90deg, #1d4ed8, #2563eb);
            transform: translateY(-1px);
        }

        .info-value {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: #f8fafc;
            font-size: 15px;
            color: #1e293b;
        }

        .info-value:focus {
            outline: none;
            border-color: #2563eb;
            background: #fff;
        }
    </style>
</head>

<body>
    <?php include 'sidebar_user.php'; ?>
    <div class="content">
        <div class="header-box">
            <div class="header-content">
                <div class="header-text">
                    <h2 class="welcome">👋<span class="highlight">Data Diklat Saya</span></h2>
                    <p class="subtext">Selamat datang kembali di data diklat saya. Yuk produktif hari ini 💪</p>
                </div>
                <div class="clock-box">
                    <div class="date" id="tanggal"></div>
                    <div class="time" id="jam"></div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="card-box">
                <?php if ($pegawai): ?>
                    <form id="updateForm" method="POST" enctype="multipart/form-data">
                        <div class="profile-wrapper">
                            <div class="profile-photo" style="position: relative;">
                                <img id="fotoPegawai" src="<?= !empty($pegawai['foto']) ? htmlspecialchars($pegawai['foto']) : 'https://ui-avatars.com/api/?name=' . urlencode($pegawai['nama']) . '&background=0D8ABC&color=fff&size=120' ?>" alt="Foto Pegawai">
                                <div id="ubahFotoBtn" style="position: absolute; bottom: 0; right: 0; background: #2563eb; width: 35px; height: 35px; border-radius: 50%; display: flex; justify-content: center; align-items: center; cursor: pointer; border: 2px solid #fff;" title="Ganti Foto">
                                    <span style="color:white; font-size:18px;">&#128393;</span>
                                </div>
                                <input type="file" name="foto" id="fotoInput" accept=".jpg,.jpeg,.png" style="display:none;">
                            </div>

                            <div class="profile-info">
                                <div class="info-group">
                                    <div class="info-label">Nama</div>
                                    <input type="text" name="nama" value="<?= htmlspecialchars($pegawai['nama']) ?>" class="info-value">
                                </div>

                                <div class="info-group">
                                    <div class="info-label">NIP</div>
                                    <input type="text" name="nip" value="<?= htmlspecialchars($pegawai['nip']) ?>" class="info-value">
                                </div>

                                <div class="info-group">
                                    <div class="info-label">Jabatan</div>
                                    <input type="text" name="jabatan" value="<?= htmlspecialchars($pegawai['jabatan']) ?>" class="info-value">
                                </div>

                                <?php if ($jenis_pegawai === 'non_asn'): ?>
                                    <div class="info-group">
                                        <div class="info-label">Rumpun Jabatan</div>
                                        <input type="text" name="rumpun_jabatan" value="<?= htmlspecialchars($pegawai['rumpun_jabatan']) ?>" class="info-value">
                                    </div>
                                <?php elseif ($jenis_pegawai === 'asn'): ?>
                                    <div class="info-group">
                                        <div class="info-label">Bidang</div>
                                        <input type="text" name="bidang" value="<?= htmlspecialchars($pegawai['bidang'] ?? '-') ?>" class="info-value">
                                    </div>
                                <?php endif; ?>

                                <div class="info-group">
                                    <div class="info-label">Ruang</div>
                                    <input type="text" name="ruang" value="<?= htmlspecialchars($pegawai['ruang']) ?>" class="info-value">
                                </div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit">💾 Simpan Perubahan</button>
                        </div>
                    </form>
                <?php else: ?>
                    <p>Data pegawai tidak ditemukan di pegawai non asn maupun pegawai asn.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div id="fotoModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.7); justify-content:center; align-items:center; z-index:9999;">
        <span id="closeModal" style="position:absolute; top:20px; right:30px; font-size:40px; color:white; cursor:pointer;">&times;</span>
        <img id="modalImg" src="" style="max-width:90%; max-height:90%; border-radius:12px; box-shadow:0 4px 12px rgba(0,0,0,0.5);">
    </div>

    <script>
        // Update jam dan tanggal
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

        // Elemen foto
        const fotoInput = document.getElementById('fotoInput');
        const ubahFotoBtn = document.getElementById('ubahFotoBtn');
        const fotoPegawai = document.getElementById('fotoPegawai');

        // Modal foto
        const fotoModal = document.getElementById('fotoModal');
        const modalImg = document.getElementById('modalImg');
        const closeModal = document.getElementById('closeModal');

        // Klik tombol ganti foto
        ubahFotoBtn.addEventListener('click', () => fotoInput.click());

        // Preview sebelum upload
        fotoInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    fotoPegawai.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Klik foto untuk lihat modal
        fotoPegawai.style.cursor = 'pointer';
        fotoPegawai.addEventListener('click', () => {
            modalImg.src = fotoPegawai.src;
            fotoModal.style.display = 'flex';
        });

        // Tutup modal
        closeModal.addEventListener('click', () => {
            fotoModal.style.display = 'none';
        });

        // Klik di luar gambar tutup modal
        fotoModal.addEventListener('click', (e) => {
            if (e.target === fotoModal) fotoModal.style.display = 'none';
        });

        const form = document.getElementById('updateForm');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(form);
            formData.append('ajax', '1'); // tandai sebagai request AJAX

            fetch('data_diklat.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        const alertBox = document.createElement('div');
                        alertBox.textContent = '✅ Data berhasil diperbarui!';
                        alertBox.style.position = 'fixed';
                        alertBox.style.top = '20px';
                        alertBox.style.right = '20px';
                        alertBox.style.background = '#16a34a';
                        alertBox.style.color = 'white';
                        alertBox.style.padding = '15px 25px';
                        alertBox.style.borderRadius = '8px';
                        alertBox.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
                        alertBox.style.zIndex = '9999';
                        document.body.appendChild(alertBox);

                        setTimeout(() => window.location.reload(), 1500);
                    } else {
                        alert('Gagal update: ' + data.message);
                    }
                })
                .catch(err => console.error(err));
        });
    </script>

</body>

</html>