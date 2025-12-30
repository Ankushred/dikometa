<?php
include 'auth.php'; // Allow Admin AND Staff
include 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone   = trim($_POST['phone']);
    
    // 1. Validation
    if (empty($name)) {
        $error = "Nama Anggota wajib diisi!";
    } else {
        // 2. Insert into 'members' table
        $joined_date = date('Y-m-d'); // Today
        $status = 'Active';

        // Note: New members added here will have NULL username/password initially.
        // They can be edited later or use the register.php page for self-service.
        $stmt = $conn->prepare("INSERT INTO members (name, address, phone, joined_date, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $address, $phone, $joined_date, $status);

        if ($stmt->execute()) {
            // Success -> Go back to Member List
            header("Location: anggota.php?msg=added");
            exit();
        } else {
            $error = "Gagal menyimpan: " . $conn->error;
        }
    }
}

// --- LOAD HEADER (Sidebar + Clock) ---
include 'header.php';
?>

<div class="container-fluid px-4">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-dark"><i class="fas fa-user-plus text-primary me-2"></i> Registrasi Anggota</h3>
            <p class="text-muted mb-0">Tambahkan data anggota koperasi baru ke dalam sistem.</p>
        </div>
        <a href="anggota.php" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4" role="alert">
                <i class="fas fa-info-circle fa-2x me-3"></i>
                <div>
                    <strong>Catatan Sistem:</strong>
                    <div class="small">Anggota baru akan berstatus <strong>Aktif</strong> namun belum memiliki Username/Password login. Akun login dapat diatur kemudian.</div>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h5 class="mb-0 fw-bold text-primary">Formulir Pendaftaran</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i> <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold text-uppercase">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fas fa-user"></i></span>
                                <input type="text" name="name" class="form-control" required placeholder="Sesuai KTP" autocomplete="off">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold text-uppercase">No. Telepon / WA</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fas fa-phone"></i></span>
                                <input type="text" name="phone" class="form-control" placeholder="Contoh: 0812..." autocomplete="off">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-muted fw-bold text-uppercase">Alamat Domisili</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fas fa-map-marker-alt"></i></span>
                                <textarea name="address" class="form-control" rows="3" placeholder="Jalan, RT/RW, Kelurahan..."></textarea>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan Data Anggota
                            </button>
                            <a href="anggota.php" class="btn btn-light text-muted border-0 py-2">
                                Batal
                            </a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php 
// --- LOAD FOOTER (Scripts) ---
include 'footer.php'; 
?>