<?php
include 'auth.php';
include 'config.php';

// Get ID from URL
if (!isset($_GET['id'])) {
    header("Location: laporan.php");
    exit();
}
$id = $_GET['id'];

// Fetch Existing Data
$stmt = $conn->prepare("SELECT * FROM transactions WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Fetch Members for Dropdown
$members = $conn->query("SELECT * FROM members WHERE status='Active'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Transaksi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">Edit Transaksi (ID: <?php echo $id; ?>)</h5>
                </div>
                <div class="card-body">
                    <form action="transaksi_proses.php" method="POST">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="id" value="<?php echo $data['id']; ?>">

                        <div class="mb-3">
                            <label class="form-label">Nama Anggota</label>
                            <select name="member_id" class="form-select" required>
                                <?php while($row = $members->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>" <?php echo ($row['id'] == $data['member_id']) ? 'selected' : ''; ?>>
                                        <?php echo $row['name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jenis Transaksi</label>
                            <select name="type" class="form-select" required>
                                <option value="saving_in" <?php echo ($data['type'] == 'saving_in') ? 'selected' : ''; ?>>Simpanan Masuk</option>
                                <option value="loan_out" <?php echo ($data['type'] == 'loan_out') ? 'selected' : ''; ?>>Pencairan Pinjaman</option>
                                <option value="loan_pay" <?php echo ($data['type'] == 'loan_pay') ? 'selected' : ''; ?>>Bayar Angsuran</option>
                                <option value="saving_out" <?php echo ($data['type'] == 'saving_out') ? 'selected' : ''; ?>>Penarikan Tunai</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nominal (Rp)</label>
                            <input type="number" name="amount" class="form-control" value="<?php echo $data['amount']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="trans_date" class="form-control" value="<?php echo $data['trans_date']; ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Keterangan</label>
                            <textarea name="description" class="form-control" rows="2"><?php echo $data['description']; ?></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-warning">Update Perubahan</button>
                            <a href="laporan.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>