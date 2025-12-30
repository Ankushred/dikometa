<?php
include 'auth.php';
include 'config.php';
checkAdmin(); // Only Admin

// Action Logic
if (isset($_GET['id']) && isset($_GET['act'])) {
    $id = intval($_GET['id']);
    $st = ($_GET['act'] == 'ok') ? 'approved' : 'rejected';
    $conn->query("UPDATE transactions SET status='$st' WHERE id=$id");
    header("Location: admin_approval.php");
    exit();
}

// Fetch Pending Requests
$sql = "SELECT t.*, m.name FROM transactions t 
        JOIN members m ON t.member_id = m.id 
        WHERE t.status='pending'
        ORDER BY t.trans_date ASC";
$res = $conn->query($sql);

// --- LOAD HEADER (Sidebar + Clock) ---
include 'header.php';
?>

<div class="container-fluid px-4">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h2 class="fw-bold text-dark"><i class="fas fa-tasks text-primary me-2"></i> Persetujuan Transaksi</h2>
            <p class="text-muted mb-0">Verifikasi permintaan transaksi dari anggota/staff.</p>
        </div>
        <a href="index.php" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 border-bottom border-light">
            <h6 class="mb-0 fw-bold text-primary">
                <i class="fas fa-clock me-2"></i> Menunggu Persetujuan
                <?php if($res->num_rows > 0): ?>
                    <span class="badge bg-danger ms-2 rounded-pill"><?php echo $res->num_rows; ?> Request</span>
                <?php endif; ?>
            </h6>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3">Tanggal</th>
                            <th class="py-3">Anggota</th>
                            <th class="py-3">Jenis Transaksi</th>
                            <th class="py-3">Nominal</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($res->num_rows > 0): ?>
                            <?php while($row = $res->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <i class="far fa-calendar-alt text-muted me-2"></i>
                                        <?php echo date('d M Y', strtotime($row['trans_date'])); ?>
                                    </div>
                                </td>
                                
                                <td>
                                    <span class="fw-bold text-dark"><?php echo $row['name']; ?></span>
                                </td>

                                <td>
                                    <?php 
                                    $t = $row['type'];
                                    if($t == 'saving_in') echo '<span class="badge bg-success bg-opacity-10 text-success border border-success px-3 py-2 rounded-pill"><i class="fas fa-arrow-down me-1"></i> Simpanan Masuk</span>';
                                    elseif($t == 'saving_out') echo '<span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-2 rounded-pill"><i class="fas fa-arrow-up me-1"></i> Penarikan</span>';
                                    elseif($t == 'loan_out') echo '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning px-3 py-2 rounded-pill"><i class="fas fa-hand-holding-usd me-1"></i> Ajuan Pinjaman</span>';
                                    elseif($t == 'loan_pay') echo '<span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 py-2 rounded-pill"><i class="fas fa-check-double me-1"></i> Bayar Hutang</span>';
                                    else echo '<span class="badge bg-secondary">'.$t.'</span>';
                                    ?>
                                </td>

                                <td class="fw-bold text-dark">
                                    Rp <?php echo number_format($row['amount'], 0, ',', '.'); ?>
                                </td>

                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="?id=<?php echo $row['id']; ?>&act=ok" 
                                           class="btn btn-sm btn-success px-3" 
                                           title="Terima Request"
                                           onclick="return confirm('Yakin ingin menyetujui transaksi ini?')">
                                           <i class="fas fa-check me-1"></i> Terima
                                        </a>
                                        <a href="?id=<?php echo $row['id']; ?>&act=no" 
                                           class="btn btn-sm btn-danger px-3" 
                                           title="Tolak Request"
                                           onclick="return confirm('Yakin ingin menolak transaksi ini?')">
                                           <i class="fas fa-times me-1"></i> Tolak
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                        <div class="bg-light rounded-circle p-4 mb-3">
                                            <i class="fas fa-clipboard-check fa-3x text-success opacity-50"></i>
                                        </div>
                                        <h6 class="fw-bold">Tidak ada request pending</h6>
                                        <small>Semua transaksi telah diproses.</small>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
// --- LOAD FOOTER (Scripts) ---
include 'footer.php'; 
?>