<?php
include 'auth.php';
include 'config.php';

// Fetch Active Members
$members = $conn->query("SELECT * FROM members WHERE status='Active' ORDER BY name ASC");

// --- LOAD HEADER ---
include 'header.php';
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1px solid #ced4da !important;
        border-radius: 0.375rem !important;
    }
    .select2-selection__rendered {
        line-height: 36px !important;
        padding-left: 12px !important;
    }
    .select2-selection__arrow {
        height: 36px !important;
    }
</style>

<div class="container-fluid px-4">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-dark"><i class="fas fa-plus-circle text-success me-2"></i> Input Transaksi Baru</h3>
            <p class="text-muted mb-0">Catat transaksi keuangan harian koperasi.</p>
        </div>
        <a href="index.php" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            
            <?php if($_SESSION['role'] == 'admin'): ?>
                <div class="alert alert-info border-start border-5 border-info shadow-sm d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-user-shield fa-lg me-3"></i>
                    <div>
                        <strong>Mode Admin:</strong> Transaksi yang Anda input akan langsung berstatus <strong>Disetujui (Approved)</strong>.
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-warning border-start border-5 border-warning shadow-sm d-flex align-items-center mb-4" role="alert">
                    <i class="fas fa-clock fa-lg me-3"></i>
                    <div>
                        <strong>Mode Staff:</strong> Transaksi akan disimpan sebagai <strong>Pending</strong> dan menunggu persetujuan Admin.
                    </div>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h5 class="mb-0 fw-bold text-dark">Formulir Transaksi</h5>
                </div>
                <div class="card-body p-4">
                    <form action="transaksi_proses.php" method="POST">
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold text-uppercase">Nama Anggota</label>
                                <select name="member_id" class="form-select select2-member" required>
                                    <option value="">-- Cari Nama Anggota --</option>
                                    <?php while($row = $members->fetch_assoc()): ?>
                                        <option value="<?php echo $row['id']; ?>">
                                            <?php echo $row['name']; ?> (ID: <?php echo $row['id']; ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold text-uppercase">Tanggal Transaksi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="far fa-calendar-alt"></i></span>
                                    <input type="date" name="trans_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold text-uppercase">Jenis Transaksi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-list-ul"></i></span>
                                    <select name="type" class="form-select" required>
                                        <option value="" disabled selected>-- Pilih Jenis --</option>
                                        <optgroup label="Simpanan">
                                            <option value="saving_in">📥 Simpanan Masuk (Tabung)</option>
                                            <option value="saving_out">📤 Penarikan Tunai (Ambil)</option>
                                        </optgroup>
                                        <optgroup label="Pinjaman">
                                            <option value="loan_out">💰 Pencairan Pinjaman (Kredit)</option>
                                            <option value="loan_pay">🧾 Bayar Angsuran (Cicil)</option>
                                        </optgroup>
                                        <optgroup label="Lainnya">
                                            <option value="expense">⚙️ Pengeluaran (Biaya Operasional)</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold text-uppercase">Nominal (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light fw-bold text-dark">Rp</span>
                                    <input type="number" name="amount" class="form-control fw-bold text-primary" placeholder="0" min="1000" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-muted fw-bold text-uppercase">Keterangan / Catatan</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Contoh: Setoran tunai bulan Januari..."></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="index.php" class="btn btn-light border text-muted px-4 py-2">Batal</a>
                            <button type="submit" class="btn btn-success px-4 py-2 fw-bold text-white shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan Transaksi
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-member').select2({
            placeholder: "Pilih atau Cari Anggota...",
            allowClear: true,
            width: '100%' // Fix for responsive width
        });
    });
</script>

<?php 
// --- LOAD FOOTER ---
include 'footer.php'; 
?>