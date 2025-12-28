<?php
include 'auth.php'; // Allow Admin AND Staff
include 'config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name    = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone   = trim($_POST['phone']);
    
    // 1. Validation
    if (empty($name)) {
        $error = "Nama Anggota wajib diisi!";
    } else {
        // 2. Insert into 'members' table
        $joined_date = date('Y-m-d'); // Today
        $status = 'Active';

        $stmt = $conn->prepare("INSERT INTO members (name, address, phone, joined_date, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $name, $address, $phone, $joined_date, $status);

        if ($stmt->execute()) {
            // Success -> Go back to Member List
            header("Location: anggota.php?msg=added");
            exit();
        } else {
            $error = "Gagal menyimpan: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Anggota Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-plus"></i> Registrasi Anggota Koperasi</h5>
                </div>
                <div class="card-body p-4">
                    
                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" required placeholder="Nama sesuai KTP">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. Telepon / WA</label>
                            <input type="text" name="phone" class="form-control" placeholder="0812...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat Domisili</label>
                            <textarea name="address" class="form-control" rows="3" placeholder="Alamat lengkap..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Simpan Anggota</button>
                            <a href="anggota.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>