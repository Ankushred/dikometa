<?php
include 'auth.php';
include 'config.php';

// SQL Query: Select everyone who is Admin or Staff
// Note: We are selecting 'password' in the *query* just in case we need it for logic,
// but we will NOT echo it in the HTML table below.
$sql = "SELECT * FROM users WHERE role IN ('admin', 'staff') ORDER BY username ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Pengguna - DIKOMETA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold"><i class="fas fa-users-cog text-primary"></i> Data Pengguna System</h2>
            <p class="text-muted">Daftar Admin dan Staff yang memiliki akses.</p>
        </div>
        <div>
            <a href="setting.php" class="btn btn-outline-primary me-2">
                <i class="fas fa-key"></i> Ganti Password Saya
            </a>
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="py-3 ps-4" width="5%">No</th>
                        <th class="py-3">Username</th>
                        <th class="py-3">Role (Jabatan)</th>
                        <th class="py-3">Status</th>
                        <th class="py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4 text-center"><?php echo $no++; ?></td>
                                
                                <td class="fw-bold text-dark">
                                    <?php echo $row['username']; ?>
                                    <?php if($row['username'] == $_SESSION['username']) echo '<span class="badge bg-primary ms-2">Saya</span>'; ?>
                                </td>

                                <td>
                                    <?php 
                                    if($row['role'] == 'admin') {
                                        echo '<span class="badge bg-purple text-dark border border-purple px-3 py-2" style="background-color:#e0aaff;">Administrator</span>';
                                    } else {
                                        echo '<span class="badge bg-info text-dark px-3 py-2">Staff</span>';
                                    }
                                    ?>
                                </td>

                                <td>
                                    <?php if($row['status'] == 'Active'): ?>
                                        <span class="badge bg-success rounded-pill"><i class="fas fa-check-circle"></i> Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger rounded-pill">Non-Aktif</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <?php if($row['username'] !== $_SESSION['username']): ?>
                                        <a href="#" class="btn btn-sm btn-secondary disabled" title="Edit (Coming Soon)"><i class="fas fa-edit"></i></a>
                                    <?php else: ?>
                                        <small class="text-muted">Akun Sedang Login</small>
                                    <?php endif; ?>
                                </td>

                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-5">Tidak ada data pengguna found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>