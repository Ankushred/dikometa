<?php
include 'auth.php';
include 'config.php';

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: anggota.php");
    exit();
}

$id = $_GET['id'];

// ==========================================
// 1. HANDLE STATUS CHANGE (Active <-> Inactive)
// ==========================================
if (isset($_GET['action']) && $_GET['action'] == 'toggle_status') {
    
    // First, get current status to flip it
    $check = $conn->query("SELECT status FROM members WHERE id=$id")->fetch_assoc();
    $current_status = $check['status'];
    
    // Logic: If Active -> become Inactive. If Inactive -> become Active.
    $new_status = ($current_status == 'Active') ? 'Inactive' : 'Active';
    
    // Update Database
    $conn->query("UPDATE members SET status='$new_status' WHERE id=$id");
    
    // Refresh Page
    header("Location: anggota_detail.php?id=$id&msg=status_changed");
    exit();
}

// ==========================================
// 2. FETCH DATA
// ==========================================
$stmt = $conn->prepare("SELECT * FROM members WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$member = $stmt->get_result()->fetch_assoc();

if (!$member) {
    die("Data anggota tidak ditemukan.");
}

// Calculate Financials
$save_in = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE member_id=$id AND type='saving_in'")->fetch_assoc()['total'] ?? 0;
$save_out = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE member_id=$id AND type='saving_out'")->fetch_assoc()['total'] ?? 0;
$total_savings = $save_in - $save_out;

$loan_out = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE member_id=$id AND type='loan_out'")->fetch_assoc()['total'] ?? 0;
$loan_pay = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE member_id=$id AND type='loan_pay'")->fetch_assoc()['total'] ?? 0;
$current_loan = $loan_out - $loan_pay;

// Fetch History
$trans_sql = "SELECT * FROM transactions WHERE member_id=$id ORDER BY trans_date DESC LIMIT 5";
$history = $conn->query($trans_sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Anggota - <?php echo $member['name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3><i class="fas fa-user-circle text-primary"></i> Detail Anggota</h3>
        <a href="anggota.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'status_changed'): ?>
        <div class="alert alert-success alert-dismissible fade show">
            Status anggota berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-dark text-white text-center py-4">
                    <i class="fas fa-user fa-4x mb-2"></i>
                    <h5 class="mb-0"><?php echo $member['name']; ?></h5>
                    <small>ID: <?php echo $member['id']; ?></small>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush mb-3">
                        <li class="list-group-item">
                            <strong><i class="fas fa-phone me-2 text-muted"></i> Telepon:</strong><br>
                            <?php echo $member['phone'] ? $member['phone'] : '-'; ?>
                        </li>
                        <li class="list-group-item">
                            <strong><i class="fas fa-map-marker-alt me-2 text-muted"></i> Alamat:</strong><br>
                            <?php echo $member['address'] ? $member['address'] : '-'; ?>
                        </li>
                        <li class="list-group-item">
                            <strong><i class="fas fa-calendar me-2 text-muted"></i> Bergabung:</strong><br>
                            <?php echo date('d M Y', strtotime($member['joined_date'])); ?>
                        </li>
                        <li class="list-group-item">
                            <strong><i class="fas fa-info-circle me-2 text-muted"></i> Status:</strong><br>
                            <?php if($member['status'] == 'Active'): ?>
                                <span class="badge bg-success w-100 py-2">AKTIF</span>
                            <?php else: ?>
                                <span class="badge bg-danger w-100 py-2">NON-AKTIF</span>
                            <?php endif; ?>
                        </li>
                    </ul>

                    <div class="d-grid">
                        <?php if($member['status'] == 'Active'): ?>
                            <a href="anggota_detail.php?id=<?php echo $id; ?>&action=toggle_status" 
                               class="btn btn-outline-danger btn-sm"
                               onclick="return confirm('Yakin ingin menonaktifkan anggota ini?');">
                                <i class="fas fa-ban"></i> Non-aktifkan Anggota
                            </a>
                        <?php else: ?>
                            <a href="anggota_detail.php?id=<?php echo $id; ?>&action=toggle_status" 
                               class="btn btn-outline-success btn-sm"
                               onclick="return confirm('Yakin ingin mengaktifkan kembali anggota ini?');">
                                <i class="fas fa-check-circle"></i> Aktifkan Anggota
                            </a>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card bg-success text-white shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Total Simpanan</h6>
                                    <h3 class="mb-0">Rp <?php echo number_format($total_savings, 0, ',', '.'); ?></h3>
                                </div>
                                <i class="fas fa-wallet fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-warning text-dark shadow-sm h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="card-title">Pinjaman Berjalan</h6>
                                    <h3 class="mb-0">Rp <?php echo number_format($current_loan, 0, ',', '.'); ?></h3>
                                </div>
                                <i class="fas fa-hand-holding-usd fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">5 Transaksi Terakhir</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th class="text-end">Nominal</th>
                                <th>Ket</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($history->num_rows > 0): ?>
                                <?php while($t = $history->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($t['trans_date'])); ?></td>
                                    <td>
                                        <?php 
                                        $type = $t['type'];
                                        if($type == 'saving_in') echo '<span class="badge bg-success">Simpanan Masuk</span>';
                                        elseif($type == 'saving_out') echo '<span class="badge bg-danger">Penarikan</span>';
                                        elseif($type == 'loan_out') echo '<span class="badge bg-warning text-dark">Pinjaman</span>';
                                        elseif($type == 'loan_pay') echo '<span class="badge bg-info">Bayar Cicilan</span>';
                                        ?>
                                    </td>
                                    <td class="text-end fw-bold">
                                        Rp <?php echo number_format($t['amount'], 0, ',', '.'); ?>
                                    </td>
                                    <td class="small text-muted"><?php echo $t['description']; ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center py-3">Belum ada transaksi.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>