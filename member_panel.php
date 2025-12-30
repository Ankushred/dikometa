<?php
include 'auth.php';
include 'config.php';
checkLogin();

// Security: If Admin tries to access, send to Index
if ($_SESSION['role'] == 'admin') { header("Location: index.php"); exit(); }

$my_id = $_SESSION['user_id'];

// ==========================================
// 1. HANDLE REQUEST
// ==========================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $type   = $_POST['type'];
    $amount = $_POST['amount'];
    $date   = date('Y-m-d');
    
    // Status = Pending
    $stmt = $conn->prepare("INSERT INTO transactions (member_id, type, amount, trans_date, status, created_by) VALUES (?, ?, ?, ?, 'pending', ?)");
    $stmt->bind_param("isdsi", $my_id, $type, $amount, $date, $my_id);
    
    if ($stmt->execute()) {
        $_SESSION['flash_msg'] = "Permintaan berhasil dikirim! Mohon tunggu konfirmasi Admin.";
        $_SESSION['flash_type'] = "success";
        header("Location: member_panel.php");
        exit();
    } else {
        $_SESSION['flash_msg'] = "Gagal mengirim permintaan.";
        $_SESSION['flash_type'] = "danger";
    }
}

// ==========================================
// 2. CALCULATE SAVINGS
// ==========================================
$save_in  = $conn->query("SELECT SUM(amount) FROM transactions WHERE member_id=$my_id AND type='saving_in' AND status='approved'")->fetch_row()[0] ?? 0;
$save_out = $conn->query("SELECT SUM(amount) FROM transactions WHERE member_id=$my_id AND type='saving_out' AND status='approved'")->fetch_row()[0] ?? 0;
$my_savings = $save_in - $save_out;

// ==========================================
// 3. CALCULATE LOANS
// ==========================================
$loan_taken = $conn->query("SELECT SUM(amount) FROM transactions WHERE member_id=$my_id AND type='loan_out' AND status='approved'")->fetch_row()[0] ?? 0;
$loan_paid  = $conn->query("SELECT SUM(amount) FROM transactions WHERE member_id=$my_id AND type='loan_pay' AND status='approved'")->fetch_row()[0] ?? 0;
$my_loan    = $loan_taken - $loan_paid;

// ==========================================
// 4. HISTORY
// ==========================================
$hist = $conn->query("SELECT * FROM transactions WHERE member_id=$my_id ORDER BY id DESC LIMIT 5");

// --- LOAD HEADER ---
include 'header.php';
?>

<style>
    .wallet-card {
        background: linear-gradient(135deg, #0d6efd 0%, #0099ff 100%);
        border: none;
        color: white;
        border-radius: 15px;
    }
    .loan-card {
        background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
        border: none;
        color: white;
        border-radius: 15px;
    }
    .status-badge-pending { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .status-badge-success { background-color: #d1e7dd; color: #0f5132; border: 1px solid #badbcc; }
    .status-badge-reject { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<div class="container-fluid px-4">
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="p-4 bg-white shadow-sm rounded border-start border-5 border-primary">
                <h3 class="fw-bold text-dark">Halo, <?php echo $_SESSION['username']; ?> 👋</h3>
                <p class="text-muted mb-0">Selamat datang kembali di Dashboard Anggota.</p>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['flash_msg'])): ?>
        <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i> <?php echo $_SESSION['flash_msg']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['flash_msg']); unset($_SESSION['flash_type']); ?>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-4">
            
            <div class="card wallet-card shadow mb-3">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <i class="fas fa-wallet fa-2x opacity-75"></i>
                        <span class="badge bg-white text-primary opacity-75">Simpanan</span>
                    </div>
                    <small class="text-white-50">Total Saldo Saya</small>
                    <h2 class="fw-bold mt-1 mb-0">Rp <?php echo number_format($my_savings); ?></h2>
                </div>
            </div>

            <div class="card loan-card shadow mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <i class="fas fa-hand-holding-usd fa-2x opacity-75"></i>
                        <span class="badge bg-white text-danger opacity-75">Hutang / Kredit</span>
                    </div>
                    <small class="text-white-50">Sisa Pinjaman Belum Lunas</small>
                    <h2 class="fw-bold mt-1 mb-0">Rp <?php echo number_format($my_loan); ?></h2>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-paper-plane text-primary me-2"></i> Buat Transaksi Baru</h6>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted small">Jenis Transaksi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-list"></i></span>
                                <select name="type" class="form-select">
                                    <optgroup label="Tabungan">
                                        <option value="saving_in">Setor Simpanan (Tabung)</option>
                                        <option value="saving_out">Tarik Simpanan (Ambil)</option>
                                    </optgroup>
                                    <optgroup label="Pinjaman">
                                        <option value="loan_out">Ajukan Pinjaman Baru</option>
                                        <option value="loan_pay">Bayar Cicilan Hutang</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small">Nominal (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="number" name="amount" class="form-control" required placeholder="Contoh: 50000" min="1000">
                            </div>
                        </div>
                        <button class="btn btn-primary w-100 fw-bold py-2">
                            Kirim Request <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-history text-primary me-2"></i> Riwayat 5 Transaksi Terakhir</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Tanggal</th>
                                    <th>Jenis</th>
                                    <th>Nominal</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($hist->num_rows > 0): ?>
                                    <?php while($r = $hist->fetch_assoc()): ?>
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light rounded p-2 me-3 text-secondary">
                                                    <i class="far fa-calendar-alt"></i>
                                                </div>
                                                <?php echo date('d M Y', strtotime($r['trans_date'])); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php 
                                                if($r['type'] == 'saving_in') echo '<span class="text-success fw-bold"><i class="fas fa-arrow-down me-1"></i> Setor Tunai</span>';
                                                elseif($r['type'] == 'saving_out') echo '<span class="text-danger fw-bold"><i class="fas fa-arrow-up me-1"></i> Penarikan</span>';
                                                elseif($r['type'] == 'loan_out') echo '<span class="text-warning fw-bold"><i class="fas fa-hand-holding-usd me-1"></i> Pinjaman Cair</span>';
                                                elseif($r['type'] == 'loan_pay') echo '<span class="text-primary fw-bold"><i class="fas fa-check-double me-1"></i> Bayar Hutang</span>';
                                                else echo ucfirst($r['type']);
                                            ?>
                                        </td>
                                        <td class="fw-bold text-dark">
                                            Rp <?php echo number_format($r['amount']); ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($r['status']=='pending'): ?>
                                                <span class="badge status-badge-pending rounded-pill px-3">
                                                    <i class="fas fa-clock me-1"></i> Menunggu
                                                </span>
                                            <?php elseif($r['status']=='approved'): ?>
                                                <span class="badge status-badge-success rounded-pill px-3">
                                                    <i class="fas fa-check me-1"></i> Berhasil
                                                </span>
                                            <?php else: ?>
                                                <span class="badge status-badge-reject rounded-pill px-3">
                                                    <i class="fas fa-times me-1"></i> Ditolak
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-muted">
                                            <i class="fas fa-inbox fa-2x mb-3 opacity-25"></i><br>
                                            Belum ada riwayat transaksi.
                                        </td>
                                    </tr>
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