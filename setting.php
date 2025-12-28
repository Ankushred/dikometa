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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaturan Akun - DIKOMETA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0"><i class="fas fa-user-cog text-primary"></i> Pengaturan Akun</h4>
                <a href="index.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-<?php echo $msg_type; ?> alert-dismissible fade show" role="alert">
                    <?php echo $message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-dark">Ganti Password Admin</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted">Password Lama</label>
                            <input type="password" name="current_password" class="form-control" required placeholder="Masukkan password saat ini">
                        </div>
                        
                        <hr class="my-4">

                        <div class="mb-3">
                            <label class="form-label text-muted">Password Baru</label>
                            <input type="password" name="new_password" class="form-control" required placeholder="Minimal 6 karakter" minlength="6">
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted">Konfirmasi Password Baru</label>
                            <input type="password" name="confirm_password" class="form-control" required placeholder="Ulangi password baru">
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card mt-3 border-0 shadow-sm">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-light rounded-circle p-3 me-3 text-primary">
                        <i class="fas fa-user-shield fa-2x"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Akun Login</h6>
                        <small class="text-muted">Username: <strong><?php echo $_SESSION['username']; ?></strong></small>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>