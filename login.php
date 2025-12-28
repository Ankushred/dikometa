<?php
session_start();
include 'config.php';

// If already logged in, send to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$error = "";

// Handle Login Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = md5($_POST['password']); // Encryption matching Step 1

    // Check Database
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password' AND status='Active'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        // Set Session Variables
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        // Redirect to Dashboard
        header("Location: index.php");
        exit();
    } else {
        $error = "Username atau Password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - DIKOMETA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #e9ecef; display: flex; align-items: center; justify-content: center; height: 100vh; }
        .login-card { width: 100%; max-width: 400px; padding: 20px; border-radius: 10px; }
        .brand-header { background: #17a2b8; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
    </style>
</head>
<body>

<div class="card shadow login-card p-0">
    <div class="brand-header">
        <h3 class="mb-0">DIKOMETA</h3>
        <small>Sistem Informasi Koperasi</small>
    </div>
    <div class="card-body p-4">
        <?php if($error): ?>
            <div class="alert alert-danger text-center p-2"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" placeholder="admin" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="admin123" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login Masuk</button>
            </div>
        </form>
    </div>
    <div class="card-footer text-center text-muted bg-white border-0 pb-3">
        <small>&copy; 2025 Dikometa System</small>
    </div>
</div>

</body>
</html>