<?php
include 'auth.php';
include 'config.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: anggota.php");
    exit();
}

$id = $_GET['id'];
$msg = "";
$msg_type = "";

// ==========================================
// 1. HANDLE STATUS CHANGE
// ==========================================
if (isset($_GET['action']) && $_GET['action'] == 'toggle_status') {
    $check = $conn->query("SELECT status FROM members WHERE id=$id")->fetch_assoc();
    $current_status = $check['status'];
    $new_status = ($current_status == 'Active') ? 'Inactive' : 'Active';
    $conn->query("UPDATE members SET status='$new_status' WHERE id=$id");
    header("Location: anggota_detail.php?id=$id&msg=status_changed");
    exit();
}

// ==========================================
// 2. HANDLE LOGIN CREDENTIALS (SECURED LOGIC)
// ==========================================
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_login'])) {
    
    // BACKEND CHECK: First, check if user already has credentials
    $check_existing = $conn->query("SELECT username FROM members WHERE id=$id")->fetch_assoc();
    
    if (!empty($check_existing['username'])) {
        // If username exists, BLOCK the request
        $msg = "Keamanan: Akun ini sudah memiliki akses login. Admin tidak dapat mengubahnya.";
        $msg_type = "danger";
    } else {
        // Only allow update if username is EMPTY (NULL or "")
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);

        if(empty($username) || empty($password)) {
            $msg = "Username dan Password tidak boleh kosong!";
            $msg_type = "danger";
        } else {
            // Check duplicate username
            $check = $conn->query("SELECT id FROM members WHERE username='$username' AND id != $id");
            if($check->num_rows > 0) {
                $msg = "Username '$username' sudah digunakan anggota lain!";
                $msg_type = "danger";
            } else {
                $pass_hash = md5($password);
                $stmt = $conn->prepare("UPDATE members SET username=?, password=? WHERE id=?");
                $stmt->bind_param("ssi", $username, $pass_hash, $id);
                
                if($stmt->execute()) {
                    $msg = "Akses login berhasil dibuat!";
                    $msg_type = "success";
                }
            }
        }
    }
}

// ==========================================
// 3. FETCH DATA
// ==========================================
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

if (!$member) { die("Data anggota tidak ditemukan."); }

// Financial Calculations
$save_in = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE member_id=$id AND type='saving_in' AND status='approved'")->fetch_assoc()['total'] ?? 0;
$save_out = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE member_id=$id AND type='saving_out' AND status='approved'")->fetch_assoc()['total'] ?? 0;
$total_savings = $save_in - $save_out;

$loan_out = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE member_id=$id AND type='loan_out' AND status='approved'")->fetch_assoc()['total'] ?? 0;
$loan_pay = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE member_id=$id AND type='loan_pay' AND status='approved'")->fetch_assoc()['total'] ?? 0;
$current_loan = $loan_out - $loan_pay;

// Fetch History
$trans_sql = "SELECT * FROM transactions WHERE member_id=$id ORDER BY trans_date DESC LIMIT 5";
$history = $conn->query($trans_sql);

// --- LOAD HEADER ---
include 'header.php';
?>

<div class="container-fluid px-4">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-dark"><i class="fas fa-user-circle text-primary me-2"></i> Detail Anggota</h3>
            <p class="text-muted mb-0">Informasi lengkap dan pengaturan akun.</p>
        </div>
        <a href="anggota.php" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'status_changed'): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> Status keanggotaan berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if($msg): ?>
        <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-info-circle me-2"></i> <?php echo $msg; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="d-inline-block p-1 rounded-circle border border-2 border-primary">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($member['name']); ?>&background=0d6efd&color=fff&size=128" class="rounded-circle" alt="User">
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1"><?php echo $member['name']; ?></h5>
                    <p class="text-muted mb-3">ID Anggota: #<?php echo str_pad($member['id'], 4, '0', STR_PAD_LEFT); ?></p>
                    
                    <?php if($member['status'] == 'Active'): ?>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill mb-4">
                            <i class="fas fa-check-circle me-1"></i> Status Aktif
                        </span>
                    <?php else: ?>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill mb-4">
                            <i class="fas fa-ban me-1"></i> Non-Aktif
                        </span>
                    <?php endif; ?>

                    <div class="text-start border-top pt-4">
                        <div class="mb-3">
                            <label class="small text-muted fw-bold text-uppercase">Nomor Telepon</label>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-phone text-primary me-3"></i>
                                <span class="fw-medium"><?php echo $member['phone'] ? $member['phone'] : '-'; ?></span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="small text-muted fw-bold text-uppercase">Alamat Domisili</label>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marker-alt text-primary me-3"></i>
                                <span class="fw-medium"><?php echo $member['address'] ? $member['address'] : '-'; ?></span>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="small text-muted fw-bold text-uppercase">Tanggal Bergabung</label>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar-alt text-primary me-3"></i>
                                <span class="fw-medium"><?php echo date('d F Y', strtotime($member['joined_date'])); ?></span>
                            </div>
                        </div>

                        <div class="d-grid">
                            <?php if($member['status'] == 'Active'): ?>
                                <a href="anggota_detail.php?id=<?php echo $id; ?>&action=toggle_status" 
                                   class="btn btn-outline-danger btn-sm py-2"
                                   onclick="return confirm('Yakin ingin menonaktifkan anggota ini?');">
                                    <i class="fas fa-user-slash me-2"></i> Non-aktifkan Anggota
                                </a>
                            <?php else: ?>
                                <a href="anggota_detail.php?id=<?php echo $id; ?>&action=toggle_status" 
                                   class="btn btn-outline-success btn-sm py-2"
                                   onclick="return confirm('Yakin ingin mengaktifkan kembali anggota ini?');">
                                    <i class="fas fa-user-check me-2"></i> Aktifkan Anggota
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <?php if(empty($member['username'])): ?>
                
                <div class="card shadow-sm border-0 border-top border-4 border-warning">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-key text-warning me-2"></i> Buat Akses Login</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="alert alert-warning small mb-3">
                            <i class="fas fa-exclamation-triangle me-1"></i> Anggota belum memiliki login. Silakan buat Username & Password awal.
                        </div>
                        <form method="POST">
                            <input type="hidden" name="update_login" value="1">
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">Username Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-user"></i></span>
                                    <input type="text" name="username" class="form-control" placeholder="Contoh: budi123" required autocomplete="off">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small text-muted fw-bold">Password Baru</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-lock"></i></span>
                                    <input type="text" name="password" class="form-control" placeholder="Min 6 karakter" required autocomplete="off">
                                </div>
                            </div>
                            <button class="btn btn-warning w-100 fw-bold text-dark">
                                <i class="fas fa-save me-1"></i> Simpan Kredensial
                            </button>
                        </form>
                    </div>
                </div>

            <?php else: ?>
                
                <div class="card shadow-sm border-0 border-top border-4 border-success">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold"><i class="fas fa-user-shield text-success me-2"></i> Akses Login</h6>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="mb-3">
                            <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-inline-block mb-3">
                                <i class="fas fa-lock fa-2x"></i>
                            </div>
                            <h6 class="fw-bold">Akses Login Aktif</h6>
                            <p class="text-muted small">
                                Username: <strong><?php echo $member['username']; ?></strong>
                            </p>
                        </div>
                        <div class="alert alert-light border small text-start">
                            <i class="fas fa-info-circle me-1 text-primary"></i> 
                            Demi keamanan, Admin <strong>tidak diizinkan</strong> mengubah password anggota yang sudah aktif. Anggota harus meresetnya sendiri jika lupa.
                        </div>
                    </div>
                </div>

            <?php endif; ?>

        </div>

        <div class="col-lg-8">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(45deg, #198754, #20c997); color: white;">
                        <div class="card-body p-4">
                            <p class="mb-1 opacity-75">Total Simpanan</p>
                            <h3 class="fw-bold mb-0">Rp <?php echo number_format($total_savings, 0, ',', '.'); ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(45deg, #ffc107, #ffca2c); color: #333;">
                        <div class="card-body p-4">
                            <p class="mb-1 opacity-75">Pinjaman Berjalan</p>
                            <h3 class="fw-bold mb-0">Rp <?php echo number_format($current_loan, 0, ',', '.'); ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="fas fa-history me-2"></i> 5 Transaksi Terakhir</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4 py-3">Tanggal</th>
                                    <th class="py-3">Jenis Transaksi</th>
                                    <th class="py-3">Nominal</th>
                                    <th class="py-3 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($history->num_rows > 0): ?>
                                    <?php while($t = $history->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4 text-muted small">
                                            <?php echo date('d/m/Y', strtotime($t['trans_date'])); ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $type = $t['type'];
                                            if($type == 'saving_in') echo '<span class="text-success fw-bold">Simpanan Masuk</span>';
                                            elseif($type == 'saving_out') echo '<span class="text-danger fw-bold">Penarikan</span>';
                                            elseif($type == 'loan_out') echo '<span class="text-warning fw-bold">Pinjaman Cair</span>';
                                            elseif($type == 'loan_pay') echo '<span class="text-info fw-bold">Bayar Cicilan</span>';
                                            ?>
                                        </td>
                                        <td class="fw-bold text-dark">
                                            Rp <?php echo number_format($t['amount'], 0, ',', '.'); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($t['status'] == 'approved'): ?>
                                                <i class="fas fa-check-circle text-success"></i>
                                            <?php elseif($t['status'] == 'pending'): ?>
                                                <i class="fas fa-clock text-warning"></i>
                                            <?php else: ?>
                                                <i class="fas fa-times-circle text-danger"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center py-5 text-muted">Belum ada riwayat transaksi.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// --- LOAD FOOTER ---
include 'footer.php'; 
?>