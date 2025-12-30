<?php
include 'auth.php';
include 'config.php';

// ==========================================
// 1. PAGINATION CONFIGURATION
// ==========================================
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Show 10 records per page
$offset = ($page - 1) * $limit;

// ==========================================
// 2. FILTER LOGIC
// ==========================================
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$where_clause = "";
$title_add = "";
$active_tab = 'all';
$url_params = ""; // To keep filters active when changing pages

// Build the Logic based on the Sidebar Click
if ($kategori == 'simpanan') {
    $where_clause = "WHERE t.type IN ('saving_in', 'saving_out')";
    $title_add = "Simpanan";
    $active_tab = 'simpanan';
    $url_params = "&kategori=simpanan";
} elseif ($kategori == 'pinjaman') {
    $where_clause = "WHERE t.type IN ('loan_out', 'loan_pay')";
    $title_add = "Pinjaman";
    $active_tab = 'pinjaman';
    $url_params = "&kategori=pinjaman";
} elseif ($kategori == 'kas') {
    $where_clause = "WHERE t.type = 'expense'"; 
    $title_add = "Arus Kas";
    $active_tab = 'kas';
    $url_params = "&kategori=kas";
}

// ==========================================
// 3. COUNT TOTAL RECORDS (For Pagination)
// ==========================================
$count_sql = "SELECT COUNT(*) as total 
              FROM transactions t 
              JOIN members m ON t.member_id = m.id 
              $where_clause";
$total_records = $conn->query($count_sql)->fetch_assoc()['total'];
$total_pages = ceil($total_records / $limit);

// ==========================================
// 4. MAIN DATA QUERY (With Limit)
// ==========================================
$sql = "SELECT t.*, m.name as member_name, m.id as member_id, m.username 
        FROM transactions t 
        JOIN members m ON t.member_id = m.id 
        $where_clause
        ORDER BY t.trans_date DESC, t.id DESC 
        LIMIT $offset, $limit";

$result = $conn->query($sql);

// --- LOAD HEADER ---
include 'header.php';
?>

<style>
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); color: #198754; }
    .bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); color: #dc3545; }
    .bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); color: #856404; }
    .bg-soft-info { background-color: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
    .bg-soft-primary { background-color: rgba(13, 110, 253, 0.1); color: #0d6efd; }
    
    .avatar-circle {
        width: 40px; height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    
    /* --- YOUR FIXED CSS (NO FLICKER) --- */
    .table-hover tbody tr {
        transition: background-color 0.2s ease, box-shadow 0.2s ease;
    }
    
    .table-hover tbody tr:hover {
        /* No transform scale here */
        background-color: #f1f3f5; 
        box-shadow: inset 4px 0 0 #0d6efd; 
        z-index: 1;
        position: relative;
    }
    
    /* Pagination Styles */
    .page-link { color: #333; border: none; margin: 0 2px; border-radius: 5px; }
    .page-item.active .page-link { background-color: #0d6efd; border-color: #0d6efd; color: white; box-shadow: 0 2px 5px rgba(13, 110, 253, 0.3); }
    .page-link:hover { background-color: #e9ecef; color: #0d6efd; }
</style>

<div class="container-fluid px-4">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-dark">
                <i class="fas fa-file-invoice-dollar text-primary me-2"></i>
                Laporan Transaksi <?php echo $title_add ? '<span class="text-muted fw-light">| ' . $title_add . '</span>' : ''; ?>
            </h3>
            <p class="text-muted mb-0">Rekapitulasi data keuangan dan riwayat aktivitas.</p>
        </div>
        <div class="d-flex gap-2">
            <div class="btn-group shadow-sm">
                <a href="laporan.php" class="btn btn-outline-secondary <?php echo $active_tab == 'all' ? 'active' : ''; ?>">Semua</a>
                <a href="laporan.php?kategori=simpanan" class="btn btn-outline-secondary <?php echo $active_tab == 'simpanan' ? 'active' : ''; ?>">Simpanan</a>
                <a href="laporan.php?kategori=pinjaman" class="btn btn-outline-secondary <?php echo $active_tab == 'pinjaman' ? 'active' : ''; ?>">Pinjaman</a>
            </div>
            
            <a href="transaksi_tambah.php" class="btn btn-primary shadow-sm fw-bold">
                <i class="fas fa-plus me-1"></i> Baru
            </a>
        </div>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-trash-alt me-2"></i> Data transaksi berhasil dihapus.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> Data transaksi berhasil diperbarui.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4 text-secondary text-uppercase small fw-bold" width="5%">No</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold" width="15%">Tanggal</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold" width="25%">Anggota</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold" width="15%">Jenis</th>
                            <th class="py-3 text-end text-secondary text-uppercase small fw-bold" width="15%">Nominal</th>
                            <th class="py-3 text-center text-secondary text-uppercase small fw-bold" width="10%">Status</th>
                            <th class="py-3 text-center text-secondary text-uppercase small fw-bold" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php 
                            // Start numbering based on page offset
                            $no = $offset + 1; 
                            while($row = $result->fetch_assoc()): 
                            ?>
                                <tr>
                                    <td class="ps-4 text-muted"><?php echo $no++; ?></td>
                                    
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light rounded p-2 me-2 text-secondary">
                                                <i class="far fa-calendar-alt"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo date('d M Y', strtotime($row['trans_date'])); ?></div>
                                                <div class="small text-muted" style="font-size: 0.75rem;">ID: #<?php echo str_pad($row['id'], 6, '0', STR_PAD_LEFT); ?></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($row['member_name']); ?>&background=random&color=fff&size=40" class="avatar-circle me-3 shadow-sm" alt="Avatar">
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo $row['member_name']; ?></div>
                                                <div class="small text-muted">ID: <?php echo $row['member_id']; ?></div>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <?php 
                                        $t = $row['type'];
                                        if($t == 'saving_in') echo '<span class="badge bg-soft-success border border-success rounded-pill px-3"><i class="fas fa-arrow-down me-1"></i> Simpanan</span>';
                                        elseif($t == 'loan_out') echo '<span class="badge bg-soft-warning border border-warning rounded-pill px-3"><i class="fas fa-hand-holding-usd me-1"></i> Pinjaman</span>';
                                        elseif($t == 'loan_pay') echo '<span class="badge bg-soft-info border border-info rounded-pill px-3"><i class="fas fa-check-double me-1"></i> Bayar Hutang</span>';
                                        elseif($t == 'saving_out') echo '<span class="badge bg-soft-danger border border-danger rounded-pill px-3"><i class="fas fa-arrow-up me-1"></i> Penarikan</span>';
                                        elseif($t == 'expense') echo '<span class="badge bg-secondary rounded-pill px-3">Pengeluaran</span>';
                                        else echo '<span class="badge bg-secondary">'.$t.'</span>';
                                        ?>
                                        <?php if(!empty($row['description'])): ?>
                                            <div class="small text-muted mt-1 text-truncate" style="max-width: 150px;">
                                                <i class="fas fa-comment-dots me-1"></i> <?php echo $row['description']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-end">
                                        <span class="fw-bold text-dark" style="font-size: 1.05rem;">
                                            Rp <?php echo number_format($row['amount'], 0, ',', '.'); ?>
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        <?php if($row['status'] == 'approved'): ?>
                                            <i class="fas fa-check-circle text-success fa-lg" title="Approved (Disetujui)"></i>
                                        <?php elseif($row['status'] == 'pending'): ?>
                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> Pending</span>
                                        <?php elseif($row['status'] == 'rejected'): ?>
                                            <span class="badge bg-danger"><i class="fas fa-times me-1"></i> Ditolak</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v text-secondary"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                                <li><h6 class="dropdown-header">Menu Aksi</h6></li>
                                                <li>
                                                    <a class="dropdown-item" href="transaksi_edit.php?id=<?php echo $row['id']; ?>">
                                                        <i class="fas fa-edit text-warning me-2"></i> Edit Data
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="transaksi_hapus.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Hapus transaksi ini permanen?');">
                                                        <i class="fas fa-trash-alt me-2"></i> Hapus Data
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>

                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-muted opacity-50">
                                        <i class="fas fa-file-invoice fa-3x mb-3"></i>
                                        <h6 class="fw-bold">Belum ada data transaksi</h6>
                                        <small>Data transaksi akan muncul di sini setelah diinput.</small>
                                        <a href="transaksi_tambah.php" class="btn btn-sm btn-outline-primary mt-3">Input Transaksi Sekarang</a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer bg-white py-3">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                
                <div class="text-muted small mb-2 mb-md-0">
                    Halaman <strong><?php echo $page; ?></strong> dari <strong><?php echo $total_pages; ?></strong> 
                    (Total: <?php echo number_format($total_records); ?> Data)
                </div>
                
                <nav aria-label="Page navigation">
                    <ul class="pagination pagination-sm mb-0">
                        
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo ($page - 1) . $url_params; ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        </li>

                        <?php 
                        $range = 2; // How many pages to show around current page
                        for($i = 1; $i <= $total_pages; $i++): 
                            if ($i == 1 || $i == $total_pages || ($i >= $page - $range && $i <= $page + $range)):
                        ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i . $url_params; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php 
                            elseif ($i == $page - $range - 1 || $i == $page + $range + 1): 
                        ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; endfor; ?>

                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo ($page + 1) . $url_params; ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>

                    </ul>
                </nav>
            </div>
        </div>

    </div>
</div>

<?php 
// --- LOAD FOOTER ---
include 'footer.php'; 
?>