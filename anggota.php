<?php
include 'auth.php';
include 'config.php';

// Fetch all members
$sql = "SELECT * FROM members ORDER BY name ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Master Data Anggota - DIKOMETA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">

    <div class="container mt-5">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="fw-bold"><i class="fas fa-users text-primary"></i> Master Data Anggota</h3>
                <p class="text-muted mb-0">Kelola data nasabah koperasi.</p>
            </div>
            <div>
                <a href="anggota_tambah.php" class="btn btn-success shadow-sm me-2">
                    <i class="fas fa-user-plus"></i> Tambah Anggota
                </a>

                <a href="index.php" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-arrow-left"></i> Dashboard
                </a>
            </div>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> Anggota baru berhasil didaftarkan!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="py-3 ps-4">ID</th>
                            <th class="py-3">Nama Lengkap</th>
                            <th class="py-3">No. Telepon</th>
                            <th class="py-3">Status</th>
                            <th class="py-3">Bergabung Sejak</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-4"><?php echo $row['id']; ?></td>
                                    <td class="fw-bold text-dark"><?php echo $row['name']; ?></td>
                                    <td><?php echo $row['phone'] ? $row['phone'] : '-'; ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'Active'): ?>
                                            <span class="badge bg-success rounded-pill">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger rounded-pill">Tidak Aktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <i class="far fa-calendar-alt text-muted me-1"></i>
                                        <?php echo date('d M Y', strtotime($row['joined_date'])); ?>
                                    </td>
                                    <td class="text-center">
                                        <a href="anggota_detail.php?id=<?php echo $row['id']; ?>"
                                            class="btn btn-sm btn-info text-white">
                                            <i class="fas fa-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Belum ada data anggota.</td>
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