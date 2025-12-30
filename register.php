<?php
include 'config.php';

$msg = "";
$msg_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = trim($_POST['name']);
    $user  = trim($_POST['username']);
    $pass  = md5($_POST['password']); // Using MD5 to match your login logic
    $phone = trim($_POST['phone']);
    $addr  = trim($_POST['address']);
    $date  = date('Y-m-d');

    // 1. Validation
    if (empty($name) || empty($user) || empty($_POST['password'])) {
        $msg = "Harap isi semua kolom wajib!";
        $msg_type = "danger";
    } else {
        // 2. Check Duplicate
        $stmt = $conn->prepare("SELECT id FROM members WHERE username = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $msg = "Username <strong>'$user'</strong> sudah digunakan. Silakan pilih yang lain.";
            $msg_type = "danger";
        } else {
            // 3. Insert with Default 'Inactive'
            $stmt_insert = $conn->prepare("INSERT INTO members (name, username, password, phone, address, joined_date, status) VALUES (?, ?, ?, ?, ?, ?, 'Inactive')");
            $stmt_insert->bind_param("ssssss", $name, $user, $pass, $phone, $addr, $date);
            
            if ($stmt_insert->execute()) {
                $msg = "Pendaftaran berhasil! Silakan tunggu Admin mengaktifkan akun Anda.";
                $msg_type = "success";
            } else {
                $msg = "Terjadi kesalahan sistem.";
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Anggota - DIKOMETA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background: linear-gradient(135deg, #0dcaf0 0%, #0d6efd 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px 0; /* Add padding for scrolling on small screens */
        }
        
        .register-container {
            width: 100%;
            padding: 15px;
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .brand-header {
            background: #fff;
            padding: 25px 20px 10px;
            text-align: center;
        }

        .logo-icon {
            background: linear-gradient(45deg, #198754, #20c997); /* Greenish for Register */
            color: white;
            width: 60px;
            height: 60px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 26px;
            margin-bottom: 10px;
            box-shadow: 0 5px 15px rgba(32, 201, 151, 0.4);
        }

        .form-control {
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #ced4da;
            font-size: 0.95rem;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #20c997;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-radius: 8px 0 0 8px;
            border-right: none;
            color: #6c757d;
            width: 45px;
            justify-content: center;
        }
        
        .input-group .form-control { border-left: none; }

        .btn-register {
            padding: 12px;
            font-weight: bold;
            font-size: 16px;
            border-radius: 8px;
            background: linear-gradient(90deg, #198754, #20c997);
            border: none;
            transition: all 0.3s;
            color: white;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3);
            color: white;
        }

        .toggle-password { cursor: pointer; z-index: 10; }
        
        .form-label { font-size: 0.85rem; font-weight: 600; color: #6c757d; margin-bottom: 5px; }
    </style>
</head>
<body>

<div class="register-container">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            
            <div class="card">
                <div class="brand-header">
                    <div class="logo-icon">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <h4 class="fw-bold text-dark">Registrasi Anggota</h4>
                    <p class="text-muted small mb-0">Gabung menjadi bagian dari Koperasi DIKOMETA</p>
                </div>

                <div class="card-body p-4 pt-2">
                    
                    <?php if($msg): ?>
                        <div class="alert alert-<?php echo $msg_type; ?> d-flex align-items-center mb-4 small" role="alert">
                            <?php if($msg_type == 'success'): ?>
                                <i class="fas fa-check-circle me-2 fa-lg"></i>
                            <?php else: ?>
                                <i class="fas fa-exclamation-circle me-2 fa-lg"></i>
                            <?php endif; ?>
                            <div><?php echo $msg; ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input type="text" name="name" class="form-control" placeholder="Sesuai KTP" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="Buat username unik" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter" required>
                                <span class="input-group-text bg-white border-start-0 toggle-password" onclick="togglePass()">
                                    <i class="far fa-eye text-muted" id="toggleIcon"></i>
                                </span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nomor WhatsApp / HP</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fab fa-whatsapp"></i></span>
                                <input type="number" name="phone" class="form-control" placeholder="Contoh: 0812..." required>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Alamat Domisili</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                                <textarea name="address" class="form-control" rows="2" placeholder="Alamat lengkap..." required></textarea>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-register">
                                DAFTAR SEKARANG <i class="fas fa-paper-plane ms-2"></i>
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="small text-muted mb-0">Sudah punya akun?</p>
                            <a href="login.php" class="fw-bold text-decoration-none text-success">Login Disini</a>
                        </div>

                    </form>
                </div>
                
                <div class="card-footer bg-light text-center py-3 border-0">
                    <small class="text-muted text-opacity-50">&copy; 2025 Dikometa System</small>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    function togglePass() {
        var passInput = document.getElementById("password");
        var icon = document.getElementById("toggleIcon");
        
        if (passInput.type === "password") {
            passInput.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            passInput.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>

</body>
</html>