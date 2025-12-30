<?php
include 'auth.php';
include 'config.php';

// Security: Admins only
checkAdmin(); 

// Get ID
if (!isset($_GET['id'])) {
    header("Location: laporan.php");
    exit();
}
$id = $_GET['id'];

// Fetch Existing Data
$stmt = $conn->prepare("SELECT * FROM transactions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Data tidak ditemukan!");
}

// Fetch Members
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
            <h3 class="fw-bold text-dark"><i class="fas fa-edit text-warning me-2"></i> Edit Data Transaksi</h3>
            <p class="text-muted mb-0">Ubah detail atau status transaksi ID #<?php echo $id; ?>.</p>
        </div>
        <a href="laporan.php" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h5 class="mb-0 fw-bold text-dark">Formulir Perubahan</h5>
                </div>
                <div class="card-body p-4">
                    <form action="transaksi_proses.php" method="POST">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold text-uppercase">Nama Anggota</label>
                                <select name="member_id" class="form-select select2-member" required>
                                    <?php while($row = $members->fetch_assoc()): ?>
                                        <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $data['member_id']) ? 'selected' : ''; ?>>
                                            <?php echo $row['name']; ?> (ID: <?php echo $row['id']; ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold text-uppercase">Tanggal Transaksi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="far fa-calendar-alt"></i></span>
                                    <input type="date" name="trans_date" class="form-control" value="<?php echo $data['trans_date']; ?>" required>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold text-uppercase">Jenis Transaksi</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-list-ul"></i></span>
                                    <select name="type" class="form-select" required>
                                        <optgroup label="Simpanan">
                                            <option value="saving_in" <?php echo ($data['type'] == 'saving_in') ? 'selected' : ''; ?>>📥 Simpanan Masuk</option>
                                            <option value="saving_out" <?php echo ($data['type'] == 'saving_out') ? 'selected' : ''; ?>>📤 Penarikan Tunai</option>
                                        </optgroup>
                                        <optgroup label="Pinjaman">
                                            <option value="loan_out" <?php echo ($data['type'] == 'loan_out') ? 'selected' : ''; ?>>💰 Pencairan Pinjaman</option>
                                            <option value="loan_pay" <?php echo ($data['type'] == 'loan_pay') ? 'selected' : ''; ?>>🧾 Bayar Angsuran</option>
                                        </optgroup>
                                        <optgroup label="Lainnya">
                                            <option value="expense" <?php echo ($data['type'] == 'expense') ? 'selected' : ''; ?>>⚙️ Pengeluaran Operasional</option>
                                        </optgroup>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted fw-bold text-uppercase">Status Persetujuan</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light"><i class="fas fa-check-circle"></i></span>
                                    <select name="status" class="form-select fw-bold" required>
                                        <option value="pending" class="text-warning" <?php echo ($data['status'] == 'pending') ? 'selected' : ''; ?>>⏳ Pending (Menunggu)</option>
                                        <option value="approved" class="text-success" <?php echo ($data['status'] == 'approved') ? 'selected' : ''; ?>>✅ Approved (Disetujui)</option>
                                        <option value="rejected" class="text-danger" <?php echo ($data['status'] == 'rejected') ? 'selected' : ''; ?>>❌ Rejected (Ditolak)</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold text-uppercase">Nominal (Rp)</label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light fw-bold text-dark">Rp</span>
                                <input type="number" name="amount" class="form-control fw-bold text-primary" value="<?php echo $data['amount']; ?>" required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-muted fw-bold text-uppercase">Keterangan / Catatan</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Contoh: Setoran tunai bulan Januari..."><?php echo $data['description']; ?></textarea>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="laporan.php" class="btn btn-light border text-muted px-4 py-2">Batal</a>
                            <button type="submit" class="btn btn-warning px-4 py-2 fw-bold text-dark shadow-sm">
                                <i class="fas fa-save me-2"></i> Update Perubahan
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
            placeholder: "Pilih Anggota...",
            allowClear: true,
            width: '100%' // Fix for responsive width
        });
    });
</script>

<?php 
// --- LOAD FOOTER ---
include 'footer.php'; 
?>