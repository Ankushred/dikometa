<?php
include 'config.php';

// SQL Query: Join transactions with members to get the Name
// We order by Date DESC (Newest first)
$sql = "SELECT t.*, m.name as member_name, m.id as member_id 
        FROM transactions t 
        JOIN members m ON t.member_id = m.id 
        ORDER BY t.trans_date DESC, t.id DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi - DIKOMETA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .table-card { border-radius: 8px; overflow: hidden; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0 fw-bold text-dark"><i class="fas fa-file-invoice-dollar text-primary me-2"></i>Laporan Transaksi</h2>
            <p class="text-muted mb-0">Rekapitulasi data keuangan koperasi</p>
        </div>
        <div>
            <a href="transaksi_tambah.php" class="btn btn-success shadow-sm me-2">
                <i class="fas fa-plus-circle me-1"></i> Input Baru
            </a>
            <a href="index.php" class="btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <div class="card table-card">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="py-3 ps-4" width="5%">No</th>
                        <th class="py-3" width="15%">Tanggal</th>
                        <th class="py-3" width="20%">Nama Anggota</th>
                        <th class="py-3" width="20%">Jenis Transaksi</th>
                        <th class="py-3 text-end" width="15%">Nominal (Rp)</th>
                        <th class="py-3 pe-4">Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php $no = 1; while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="ps-4 text-center"><?php echo $no++; ?></td>
                                <td>
                                    <i class="far fa-calendar-alt text-muted me-1"></i>
                                    <?php echo date('d M Y', strtotime($row['trans_date'])); ?>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark"><?php echo $row['member_name']; ?></span><br>
                                    <small class="text-muted" style="font-size: 0.85rem;">ID Anggota: <?php echo $row['member_id']; ?></small>
                                </td>
                                <td>
                                    <?php 
                                    // Visual Badges for different types
                                    $t = $row['type'];
                                    if($t == 'saving_in') echo '<span class="badge bg-success rounded-pill px-3"><i class="fas fa-arrow-down me-1"></i> Simpanan Masuk</span>';
                                    elseif($t == 'loan_out') echo '<span class="badge bg-warning text-dark rounded-pill px-3"><i class="fas fa-hand-holding-usd me-1"></i> Pencairan Pinjaman</span>';
                                    elseif($t == 'loan_pay') echo '<span class="badge bg-info text-dark rounded-pill px-3"><i class="fas fa-check-circle me-1"></i> Bayar Angsuran</span>';
                                    elseif($t == 'saving_out') echo '<span class="badge bg-danger rounded-pill px-3"><i class="fas fa-arrow-up me-1"></i> Penarikan Tunai</span>';
                                    else echo '<span class="badge bg-secondary">'.$t.'</span>';
                                    ?>
                                </td>
                                <td class="text-end fw-bold text-dark font-monospace" style="font-size: 1.1rem;">
                                    Rp <?php echo number_format($row['amount'], 0, ',', '.'); ?>
                                </td>
                                <td class="pe-4 text-secondary small">
                                    <?php echo $row['description'] ? $row['description'] : '-'; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <img src="https://cdn-icons-png.flaticon.com/512/7486/7486777.png" width="80" class="mb-3 opacity-50" alt="No Data">
                                <p class="text-muted fw-bold">Belum ada data transaksi ditemukan.</p>
                                <a href="transaksi_tambah.php" class="btn btn-sm btn-outline-primary">Tambah Data Sekarang</a>
                            </td>
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