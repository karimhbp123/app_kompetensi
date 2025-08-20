<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<div id="openSidebarBtn" style="position: fixed; top: 20px; left: 20px; z-index: 9999; background-color: #1e293b; color: white; padding: 8px 12px; border-radius: 6px; cursor: pointer; display: none;">
  ☰
</div>

<div id="sidebar" style="width: 240px; background-color: #1e293b; color: white; height: 100vh; position: fixed; left: 0; top: 0; box-shadow: 2px 0 10px rgba(0,0,0,0.3); font-family: 'Segoe UI', sans-serif; display: flex; flex-direction: column; transition: left 0.3s ease; z-index: 9998;">
  <div style="padding: 24px 20px 12px 20px; border-bottom: 1px solid #334155; display: flex; justify-content: space-between; align-items: center;">
    <h2 style="margin: 0; font-size: 20px; font-weight: bold; color: #60a5fa;">📋 User Panel</h2>
    <span onclick="closeSidebar()" style="cursor: pointer; font-size: 18px; color: #f87171;">❌</span>
  </div>
  <ul style="list-style: none; padding: 20px 0; margin: 0; flex: 1;">
    <li>
      <a href="dashboard.php" style="display:flex;align-items:center;gap:12px;padding:12px 24px;color:white;text-decoration:none;border-radius:8px; background-color:<?= $current == 'dashboard.php' ? '#334155' : 'transparent' ?>;"
        onmouseover="this.style.background='#334155'" onmouseout="this.style.background='<?= $current == 'dashboard.php' ? '#334155' : 'transparent' ?>'">
        <span style="font-size:18px;">🏠</span> <span>Dashboard</span>
      </a>
    </li>
    <li>
      <a href="data_diklat.php" style="display:flex;align-items:center;gap:12px;padding:12px 24px;color:white;text-decoration:none;border-radius:8px; background-color:<?= $current == 'data_diklat.php' ? '#334155' : 'transparent' ?>;"
        onmouseover="this.style.background='#334155'" onmouseout="this.style.background='<?= $current == 'data_diklat.php' ? '#334155' : 'transparent' ?>'">
        <span style="font-size:18px;">📄</span> <span>Data Profil</span>
      </a>
    </li>
    <li>
      <a href="upload_sertifikat_user.php" style="display:flex;align-items:center;gap:12px;padding:12px 24px;color:white;text-decoration:none;border-radius:8px; background-color:<?= $current == 'upload_sertifikat_user.php' ? '#334155' : 'transparent' ?>;"
        onmouseover="this.style.background='#334155'" onmouseout="this.style.background='<?= $current == 'upload_sertifikat_user.php' ? '#334155' : 'transparent' ?>'">
        <span style="font-size:18px;">⬆️</span> <span>Upload Sertifikat</span>
      </a>
    </li>
  </ul>
  <div style="padding: 20px; border-top: 1px solid #334155;">
    <a href="../auth/logout.php" style="display:flex;align-items:center;gap:12px;padding:12px 24px;color:#f87171;text-decoration:none;border-radius:8px;"
      onmouseover="this.style.background='#7f1d1d';this.style.color='#fff'" onmouseout="this.style.background='transparent';this.style.color='#f87171'">
      <span style="font-size:18px;">🚪</span> <span>Keluar</span>
    </a>
  </div>
</div>

<script>
  const sidebar = document.getElementById('sidebar');
  const openBtn = document.getElementById('openSidebarBtn');
  function closeSidebar() {
    sidebar.style.left = '-260px';
    openBtn.style.display = 'block';
  }
  openBtn.addEventListener('click', function() {
    sidebar.style.left = '0';
    openBtn.style.display = 'none';
  });
</script>
