<?php
include 'auth.php';
include 'config.php';

// 1. Check if a category filter was clicked
$kategori = isset($_GET['kategori']) ? $_GET['kategori'] : '';
$where_clause = "";
$title_add = "";

// 2. Build the Logic based on the Sidebar Click
if ($kategori == 'simpanan') {
    $where_clause = "WHERE t.type IN ('saving_in', 'saving_out')";
    $title_add = "- Khusus Simpanan";
} elseif ($kategori == 'pinjaman') {
    $where_clause = "WHERE t.type IN ('loan_out', 'loan_pay')";
    $title_add = "- Khusus Pinjaman";
} elseif ($kategori == 'kas') {
    // Shows everything except pure database adjustments (optional)
    $where_clause = ""; 
    $title_add = "- Arus Kas";
}

// 3. The SQL Query (Modified with WHERE clause)
$sql = "SELECT t.*, m.name as member_name, m.id as member_id 
        FROM transactions t 
        JOIN members m ON t.member_id = m.id 
        $where_clause
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

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-trash-alt me-2"></i> Data transaksi berhasil dihapus.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> Data transaksi berhasil diperbarui.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card table-card">
        <div class="card-body p-0">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="py-3 ps-4" width="5%">No</th>
                        <th class="py-3" width="12%">Tanggal</th>
                        <th class="py-3" width="20%">Nama Anggota</th>
                        <th class="py-3" width="18%">Jenis Transaksi</th>
                        <th class="py-3 text-end" width="15%">Nominal (Rp)</th>
                        <th class="py-3">Keterangan</th>
                        <th class="py-3 text-center" width="10%">Aksi</th>
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
                                    <small class="text-muted" style="font-size: 0.85rem;">ID: <?php echo $row['member_id']; ?></small>
                                </td>
                                <td>
                                    <?php 
                                    // Visual Badges
                                    $t = $row['type'];
                                    if($t == 'saving_in') echo '<span class="badge bg-success rounded-pill px-3">Simpanan Masuk</span>';
                                    elseif($t == 'loan_out') echo '<span class="badge bg-warning text-dark rounded-pill px-3">Pencairan Pinjaman</span>';
                                    elseif($t == 'loan_pay') echo '<span class="badge bg-info text-dark rounded-pill px-3">Bayar Angsuran</span>';
                                    elseif($t == 'saving_out') echo '<span class="badge bg-danger rounded-pill px-3">Penarikan Tunai</span>';
                                    else echo '<span class="badge bg-secondary">'.$t.'</span>';
                                    ?>
                                </td>
                                <td class="text-end fw-bold text-dark font-monospace" style="font-size: 1.1rem;">
                                    Rp <?php echo number_format($row['amount'], 0, ',', '.'); ?>
                                </td>
                                <td class="text-secondary small">
                                    <?php echo $row['description'] ? $row['description'] : '-'; ?>
                                </td>
                                
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="transaksi_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning text-white" title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="transaksi_hapus.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" title="Hapus Data" onclick="return confirm('Apakah Anda yakin ingin menghapus data transaksi ini? Data tidak dapat dikembalikan.');">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5">
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