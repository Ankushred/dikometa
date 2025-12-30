<?php
include 'auth.php';
include 'config.php';

// SQL Query
$sql = "SELECT * FROM users WHERE role IN ('admin', 'staff') ORDER BY username ASC";
$result = $conn->query($sql);

// --- LOAD HEADER ---
include 'header.php';
?>

<div class="container-fluid px-4">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-dark"><i class="fas fa-users-cog text-primary me-2"></i> Manajemen User System</h3>
            <p class="text-muted mb-0">Kelola akun Administrator dan Staff.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="setting.php" class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-key me-2"></i> Ganti Password Saya
            </a>
            <a href="index.php" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left"></i>
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3 ps-4 text-secondary text-uppercase small fw-bold" width="5%">No</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold" width="25%">Username</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold" width="15%">Role</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold" width="15%">Status</th>
                            <th class="py-3 text-secondary text-uppercase small fw-bold" width="20%">Terakhir Login</th>
                            <th class="py-3 text-center text-secondary text-uppercase small fw-bold" width="10%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4 text-muted"><?php echo $no++; ?></td>
                                    
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($row['username']); ?>&background=random&color=fff&size=35" class="rounded-circle me-3 shadow-sm" width="35" height="35" alt="Avatar">
                                            <div>
                                                <span class="fw-bold text-dark"><?php echo $row['username']; ?></span>
                                                <?php if($row['username'] == $_SESSION['username']): ?>
                                                    <span class="badge bg-primary ms-2" style="font-size: 0.7rem;">SAYA</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>

                                    <td>
                                        <?php if($row['role'] == 'admin'): ?>
                                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary px-3 rounded-pill">Administrator</span>
                                        <?php else: ?>
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info px-3 rounded-pill text-dark">Staff</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <?php if($row['status'] == 'Active'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success px-3 rounded-pill"><i class="fas fa-check-circle me-1"></i> Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 rounded-pill">Non-Aktif</span>
                                        <?php endif; ?>
                                    </td>

                                    <td class="text-muted small">
                                        <i class="far fa-clock me-1"></i> -
                                    </td>

                                    <td class="text-center">
                                        <div class="btn-group">
                                            <?php if($row['username'] !== $_SESSION['username']): ?>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Aksi Dibatasi">
                                                    <i class="fas fa-key"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary" disabled title="Aksi Dibatasi">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted small fst-italic">Akun Anda</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>

                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fas fa-users-slash fa-2x mb-3 opacity-50"></i><br>
                                    Tidak ada data pengguna ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white py-3">
            <small class="text-muted">Menampilkan <strong><?php echo $result->num_rows; ?></strong> pengguna sistem.</small>
        </div>
    </div>
</div>

<?php 
// --- LOAD FOOTER ---
include 'footer.php'; 
?>