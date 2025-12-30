<?php
include 'auth.php';
include 'config.php';

$message = "";
$msg_type = "";

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_pass = md5($_POST['current_password']);
    $new_pass     = $_POST['new_password'];
    $confirm_pass = $_POST['confirm_password'];
    $user_id      = $_SESSION['user_id'];

    // 1. Check if New Password matches Confirmation
    if ($new_pass !== $confirm_pass) {
        $message = "Password baru dan konfirmasi tidak cocok!";
        $msg_type = "danger";
    } else {
        // 2. Verify Old Password
        $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = $result->fetch_assoc();

        if ($data['password'] !== $current_pass) {
            $message = "Password lama Anda salah!";
            $msg_type = "danger";
        } else {
            // 3. Update to New Password
            $new_hash = md5($new_pass);
            $update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update->bind_param("si", $new_hash, $user_id);
            
            if ($update->execute()) {
                $message = "Password berhasil diperbarui! Silakan login ulang.";
                $msg_type = "success";
            } else {
                $message = "Terjadi kesalahan sistem.";
                $msg_type = "danger";
            }
        }
    }
}

// --- LOAD HEADER ---
include 'header.php';
?>

<style>
    .cursor-pointer { cursor: pointer; }
    .input-group-text { background-color: #f8f9fa; border-right: none; }
    .form-control { border-left: none; }
    /* Fix focus border */
    .input-group:focus-within {
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        border-radius: 0.375rem;
    }
    .input-group:focus-within .input-group-text, 
    .input-group:focus-within .form-control {
        border-color: #86b7fe;
    }
</style>

<div class="container-fluid px-4">
    
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-4">
        <div class="mb-3 mb-md-0">
            <h3 class="fw-bold text-dark"><i class="fas fa-user-cog text-primary me-2"></i> Pengaturan Akun</h3>
            <p class="text-muted mb-0">Kelola keamanan dan informasi akun Anda.</p>
        </div>
        <a href="index.php" class="btn btn-outline-secondary shadow-sm">
            <i class="fas fa-arrow-left me-1"></i> Dashboard
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <?php if ($message): ?>
                <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show shadow-sm border-0" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-<?php echo ($msg_type == 'success') ? 'check-circle' : 'exclamation-circle'; ?> me-2 fa-lg"></i>
                        <div><?php echo $message; ?></div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-lock text-warning me-2"></i> Ganti Password</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold text-uppercase">Password Lama</label>
                            <div class="input-group border rounded">
                                <span class="input-group-text border-0"><i class="fas fa-key text-muted"></i></span>
                                <input type="password" name="current_password" id="current_pass" class="form-control border-0" required placeholder="Masukkan password saat ini">
                                <span class="input-group-text border-0 bg-white cursor-pointer" onclick="togglePass('current_pass', this)">
                                    <i class="far fa-eye text-muted"></i>
                                </span>
                            </div>
                        </div>
                        
                        <hr class="my-4 border-light">

                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold text-uppercase">Password Baru</label>
                            <div class="input-group border rounded">
                                <span class="input-group-text border-0"><i class="fas fa-lock text-muted"></i></span>
                                <input type="password" name="new_password" id="new_pass" class="form-control border-0" required placeholder="Minimal 6 karakter" minlength="6">
                                <span class="input-group-text border-0 bg-white cursor-pointer" onclick="togglePass('new_pass', this)">
                                    <i class="far fa-eye text-muted"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-muted fw-bold text-uppercase">Konfirmasi Password</label>
                            <div class="input-group border rounded">
                                <span class="input-group-text border-0"><i class="fas fa-check-double text-muted"></i></span>
                                <input type="password" name="confirm_password" id="confirm_pass" class="form-control border-0" required placeholder="Ulangi password baru">
                                <span class="input-group-text border-0 bg-white cursor-pointer" onclick="togglePass('confirm_pass', this)">
                                    <i class="far fa-eye text-muted"></i>
                                </span>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary py-2 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm bg-light">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="bg-white rounded-circle p-3 shadow-sm me-3 text-primary">
                            <i class="fas fa-user-shield fa-lg"></i>
                        </div>
                        <div>
                            <small class="text-muted text-uppercase fw-bold">Akun Login Saat Ini</small>
                            <h5 class="mb-0 fw-bold text-dark"><?php echo $_SESSION['username']; ?></h5>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill px-2">Administrator</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function togglePass(inputId, iconSpan) {
        const input = document.getElementById(inputId);
        const icon = iconSpan.querySelector('i');
        
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

<?php 
// --- LOAD FOOTER ---
include 'footer.php'; 
?>