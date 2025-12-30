<div class="border-end" id="sidebar-wrapper">
    <div class="sidebar-heading"><i class="fas fa-university me-2"></i> DIKOMETA</div>
    <div class="list-group list-group-flush">
        <?php 
        $p = basename($_SERVER['PHP_SELF']); 
        $role = $_SESSION['role'] ?? ''; // Get the logged-in role
        ?>
        
        <?php if($role == 'admin'): ?>
            <div class="sidebar-heading bg-dark py-2" style="font-size: 0.75rem; letter-spacing: 1px;">MENU ADMIN</div>

            <a href="index.php" class="list-group-item list-group-item-action <?php echo ($p == 'index.php') ? 'active' : ''; ?>">
                <i class="fas fa-home"></i> Beranda
            </a>

            <a href="admin_approval.php" class="list-group-item list-group-item-action <?php echo ($p == 'admin_approval.php') ? 'active' : ''; ?>">
                <i class="fas fa-check-double"></i> Approval
            </a>

            <a href="laporan.php?kategori=kas" class="list-group-item list-group-item-action <?php echo (isset($_GET['kategori']) && $_GET['kategori'] == 'kas') ? 'active' : ''; ?>">
                <i class="fas fa-money-bill-wave"></i> Transaksi Kas
            </a>

            <a href="laporan.php?kategori=simpanan" class="list-group-item list-group-item-action <?php echo (isset($_GET['kategori']) && $_GET['kategori'] == 'simpanan') ? 'active' : ''; ?>">
                <i class="fas fa-wallet"></i> Simpanan
            </a>

            <a href="laporan.php?kategori=pinjaman" class="list-group-item list-group-item-action <?php echo (isset($_GET['kategori']) && $_GET['kategori'] == 'pinjaman') ? 'active' : ''; ?>">
                <i class="fas fa-hand-holding-usd"></i> Pinjaman
            </a>

            <a href="laporan.php" class="list-group-item list-group-item-action <?php echo ($p == 'laporan.php' && !isset($_GET['kategori'])) ? 'active' : ''; ?>">
                <i class="fas fa-file-alt"></i> Laporan Lengkap
            </a>

            <a href="anggota.php" class="list-group-item list-group-item-action <?php echo ($p == 'anggota.php' || $p == 'anggota_tambah.php' || $p == 'anggota_detail.php') ? 'active' : ''; ?>">
                <i class="fas fa-database"></i> Master Data
            </a>

            <a href="users.php" class="list-group-item list-group-item-action <?php echo ($p == 'users.php') ? 'active' : ''; ?>">
                <i class="fas fa-user-shield"></i> Data Pengguna
            </a>

            <a href="setting.php" class="list-group-item list-group-item-action <?php echo ($p == 'setting.php') ? 'active' : ''; ?>">
                <i class="fas fa-cogs"></i> Setting
            </a>
        <?php endif; ?>


        <?php if($role == 'member'): ?>
            <div class="sidebar-heading bg-dark py-2" style="font-size: 0.75rem; letter-spacing: 1px;">MENU ANGGOTA</div>
            
            <a href="member_panel.php" class="list-group-item list-group-item-action <?php echo ($p == 'member_panel.php') ? 'active' : ''; ?>">
                <i class="fas fa-wallet"></i> Panel Saya
            </a>
        <?php endif; ?>


        <a href="logout.php" class="list-group-item list-group-item-action text-danger mt-4">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>