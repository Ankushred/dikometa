<?php
include 'config.php';

// Fetch Active Members
// We use 'name' here because the SQL script created the column as 'name'
$members = $conn->query("SELECT * FROM members WHERE status='Active'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Transaksi - DIKOMETA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Input Transaksi Baru</h5>
                </div>
                <div class="card-body">
                    <form action="transaksi_proses.php" method="POST">
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Anggota</label>
                            <select name="member_id" class="form-select" required>
                                <option value="">-- Pilih Anggota --</option>
                                <?php while($row = $members->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>">
                                        <?php echo $row['name']; ?> (ID: <?php echo $row['id']; ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Transaksi</label>
                            <select name="type" class="form-select" required>
                                <option value="saving_in">Simpanan Masuk (Tabungan)</option>
                                <option value="loan_out">Pencairan Pinjaman (Kredit)</option>
                                <option value="loan_pay">Bayar Angsuran</option>
                                <option value="saving_out">Penarikan Tunai</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nominal (Rp)</label>
                            <input type="number" name="amount" class="form-control" placeholder="Contoh: 100000" min="1000" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Transaksi</label>
                            <input type="date" name="trans_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="Catatan tambahan..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Simpan Transaksi</button>
                            <a href="index.php" class="btn btn-secondary">Batal / Kembali</a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>