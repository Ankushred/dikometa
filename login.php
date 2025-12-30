<?php
include 'config.php';
include 'auth.php';
redirectIfLoggedIn();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = md5($_POST['password']); 

    // A. CHECK ADMIN (users table)
    $stmt = $conn->prepare("SELECT id, role, status FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role']    = 'admin'; 
        $_SESSION['username']= $username;
        header("Location: index.php");
        exit();
    }

    // B. CHECK MEMBER (members table)
    $stmt2 = $conn->prepare("SELECT id, name, status FROM members WHERE username=? AND password=?");
    $stmt2->bind_param("ss", $username, $password);
    $stmt2->execute();
    $res2 = $stmt2->get_result();

    if ($res2->num_rows > 0) {
        $row = $res2->fetch_assoc();
        
        if ($row['status'] == 'Inactive') {
            $error = "Akun Anda belum diaktifkan Admin.";
        } else {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['role']    = 'member';
            $_SESSION['username']= $row['name'];
            header("Location: member_panel.php");
            exit();
        }
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DIKOMETA</title>
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
        }
        
        .login-container {
            width: 100%;
            padding: 15px;
        }

        .card {
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }

        .login-header {
            background: #fff;
            padding: 30px 20px 10px;
            text-align: center;
        }

        .logo-icon {
            background: linear-gradient(45deg, #0dcaf0, #0d6efd);
            color: white;
            width: 60px;
            height: 60px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-size: 28px;
            margin-bottom: 15px;
            box-shadow: 0 5px 15px rgba(13, 202, 240, 0.4);
        }

        .form-control {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ced4da;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-radius: 8px 0 0 8px;
            border-right: none;
        }
        
        /* Fix input border radius when attached to group */
        .input-group .form-control {
            border-left: none; 
        }

        .btn-login {
            padding: 12px;
            font-weight: bold;
            font-size: 16px;
            border-radius: 8px;
            background: linear-gradient(90deg, #0d6efd, #0a58ca);
            border: none;
            transition: all 0.3s;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
        }

        .toggle-password {
            cursor: pointer;
            z-index: 10;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            
            <div class="card">
                <div class="login-header">
                    <div class="logo-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <h4 class="fw-bold text-dark">DIKOMETA</h4>
                    <p class="text-muted small">Sistem Informasi Koperasi Digital</p>
                </div>

                <div class="card-body p-4 pt-2">
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <div class="small"><?php echo $error; ?></div>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user text-primary"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus autocomplete="username">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label small text-muted fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock text-primary"></i></span>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                                <span class="input-group-text bg-white border-start-0 toggle-password" onclick="togglePass()">
                                    <i class="far fa-eye text-muted" id="toggleIcon"></i>
                                </span>
                            </div>
                        </div>

                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-primary btn-login text-white">
                                MASUK SEKARANG <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="small text-muted mb-0">Belum menjadi anggota?</p>
                            <a href="register.php" class="fw-bold text-decoration-none text-primary">Daftar Akun Baru</a>
                        </div>

                    </form>
                </div>
                
                <div class="card-footer bg-light text-center py-3 border-0">
                    <small class="text-muted text-opacity-50">&copy; 2025 Dikometa System v1.0</small>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Simple Script to Toggle Password Visibility
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