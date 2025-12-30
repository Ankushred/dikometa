<?php
include 'auth.php';
include 'config.php';

// 1. SEARCH LOGIC
$search = isset($_GET['q']) ? $_GET['q'] : '';
$where_clause = "";

if(!empty($search)) {
    $search_safe = $conn->real_escape_string($search);
    $where_clause = "WHERE name LIKE '%$search_safe%' OR id = '$search_safe'";
}

// 2. FETCH DATA
$sql = "SELECT * FROM members $where_clause ORDER BY name ASC";
$result = $conn->query($sql);

// --- LOAD HEADER ---
include 'header.php';
?>

<div class="container-fluid px-4">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-dark"><i class="fas fa-users text-primary me-2"></i> Master Data Anggota</h3>
            <p class="text-muted mb-0">Kelola data nasabah dan informasi keanggotaan.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="anggota_tambah.php" class="btn btn-primary shadow-sm fw-bold">
                <i class="fas fa-user-plus me-2"></i> Tambah Anggota
            </a>
            <a href="index.php" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas fa-check-circle me-2"></i> Anggota baru berhasil didaftarkan!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                        <input type="text" name="q" class="form-control border-start-0 ps-0" placeholder="Cari nama atau ID anggota..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-dark w-100">Cari</button>
                </div>
                <?php if(!empty($search)): ?>
                    <div class="col-md-2">
                        <a href="anggota.php" class="btn btn-light border w-100 text-muted">Reset</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-3" width="10%">ID</th>
                            <th class="py-3" width="30%">Nama Anggota</th>
                            <th class="py-3" width="20%">Kontak</th>
                            <th class="py-3" width="15%">Status</th>
                            <th class="py-3" width="15%">Bergabung</th>
                            <th class="py-3 text-center" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4">
                                        <span class="badge bg-light text-dark border font-monospace">
                                            #<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($row['name']); ?>&background=random&color=fff&size=40" class="rounded-circle me-3" width="40" height="40" alt="Avatar">
                                            <div>
                                                <div class="fw-bold text-dark"><?php echo $row['name']; ?></div>
                                                <?php if(!empty($row['username'])): ?>
                                                    <small class="text-success"><i class="fas fa-user-check me-1"></i>Akses Login Aktif</small>
                                                <?php else: ?>
                                                    <small class="text-muted">Belum ada akun</small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            <i class="fas fa-phone me-2"></i> <?php echo $row['phone'] ? $row['phone'] : '-'; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] == 'Active'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-3">
                                                <i class="fas fa-check-circle me-1"></i> Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill px-3">
                                                <i class="fas fa-ban me-1"></i> Non-Aktif
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="text-muted small">
                                            <?php echo date('d M Y', strtotime($row['joined_date'])); ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <a href="anggota_detail.php?id=<?php echo $row['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary shadow-sm"
                                           title="Lihat Detail">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center text-muted opacity-50">
                                        <i class="fas fa-users-slash fa-3x mb-3"></i>
                                        <h6 class="fw-bold">Data tidak ditemukan</h6>
                                        <small>Coba kata kunci lain atau tambahkan anggota baru.</small>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            <small class="text-muted">Menampilkan <strong><?php echo $result->num_rows; ?></strong> data anggota.</small>
        </div>
    </div>
</div>

<?php 
// --- LOAD FOOTER ---
include 'footer.php'; 
?>