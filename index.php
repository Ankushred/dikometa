<?php
include 'auth.php';
include 'config.php';
checkAdmin(); 

// Function to format Rupiah safely
function formatRupiah($number)
{
    return number_format($number, 0, ',', '.');
}

// ==========================================
// 0. CHECK PENDING TRANSACTIONS (Notification)
// ==========================================
$pending_sql = "SELECT COUNT(*) as total FROM transactions WHERE status='pending'";
$pending_count = $conn->query($pending_sql)->fetch_assoc()['total'];

// ==========================================
// 1. CALCULATE SAVINGS (Simpanan Anggota)
// ==========================================
$sql_save_in = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE type='saving_in' AND status='approved'")->fetch_assoc();
$sql_save_out = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE type='saving_out' AND status='approved'")->fetch_assoc();

$save_in = $sql_save_in['total'] ? $sql_save_in['total'] : 0;
$save_out = $sql_save_out['total'] ? $sql_save_out['total'] : 0;
$total_savings = $save_in - $save_out;

// ==========================================
// 2. CALCULATE LOANS (Pinjaman Kredit)
// ==========================================
$loan_count_sql = "SELECT COUNT(*) as total FROM transactions WHERE type='loan_out' AND status='approved'";
$total_loan_count = $conn->query($loan_count_sql)->fetch_assoc()['total'];

$sql_loan_out = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE type='loan_out' AND status='approved'")->fetch_assoc();
$loan_out_total = $sql_loan_out['total'] ? $sql_loan_out['total'] : 0;

$borrower_sql = "SELECT COUNT(DISTINCT member_id) as total FROM transactions WHERE type='loan_out' AND status='approved'";
$total_borrowers = $conn->query($borrower_sql)->fetch_assoc()['total'];

// ==========================================
// 3. CALCULATE COMPANY CASH (Kas Koperasi)
// ==========================================
$sql_loan_pay = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE type='loan_pay' AND status='approved'")->fetch_assoc();
$sql_expense = $conn->query("SELECT SUM(amount) as total FROM transactions WHERE type='expense' AND status='approved'")->fetch_assoc();

$loan_pay = $sql_loan_pay['total'] ? $sql_loan_pay['total'] : 0;
$expense = $sql_expense['total'] ? $sql_expense['total'] : 0;

$total_income = $save_in + $loan_pay;
$total_expense = $save_out + $loan_out_total + $expense;
$kas_balance = $total_income - $total_expense;

// ==========================================
// 4. COUNTS (Members & Users)
// ==========================================
$member_sql = "SELECT COUNT(*) as total FROM members WHERE status='Active'";
$total_members = $conn->query($member_sql)->fetch_assoc()['total'];

$user_sql = "SELECT COUNT(*) as total FROM users WHERE status='Active'";
$total_users = $conn->query($user_sql)->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIKOMETA - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary-bg: #f4f6f9;
            --sidebar-width: 250px;
        }

        body {
            background-color: var(--primary-bg);
            font-family: 'Segoe UI', sans-serif;
            overflow-x: hidden; 
        }

        /* --- RESPONSIVE SIDEBAR CSS --- */
        #wrapper {
            display: flex;
            width: 100%;
            overflow: hidden;
        }

        /* Note: Make sure sidebar.php has the id="sidebar-wrapper" and correct classes */
        #sidebar-wrapper {
            min-height: 100vh;
            margin-left: -15rem; 
            transition: margin .25s ease-out;
            background-color: #343a40;
            color: #fff;
            width: 15rem;
            flex-shrink: 0; 
        }

        #sidebar-wrapper .sidebar-heading {
            padding: 1.2rem 1.25rem;
            font-size: 1.2rem;
            font-weight: bold;
            background: #17a2b8;
            color: white;
            white-space: nowrap; 
        }

        #page-content-wrapper {
            flex-grow: 1; 
            min-width: 0; 
            width: 100%;
            transition: margin .25s ease-out;
        }

        body.sb-sidenav-toggled #sidebar-wrapper {
            margin-left: 0;
        }

        @media (min-width: 768px) {
            #sidebar-wrapper {
                margin-left: 0; 
            }

            #page-content-wrapper {
                width: 100%;
            }

            body.sb-sidenav-toggled #sidebar-wrapper {
                margin-left: -15rem;
            }
        }

        /* Links */
        .list-group-item {
            border: none;
            padding: 15px 20px;
            background-color: #343a40;
            color: #cfd8dc;
            border-left: 4px solid transparent;
        }

        .list-group-item:hover {
            background-color: #495057;
            color: #fff;
        }

        .list-group-item.active {
            background-color: #2c3034;
            color: #17a2b8;
            font-weight: bold;
            border-left: 4px solid #17a2b8;
        }

        .list-group-item i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
        }

        /* --- MODERN CARD STYLING --- */
        .small-box {
            border-radius: 10px;
            position: relative;
            display: block;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
            color: #fff;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .small-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.15);
        }

        .small-box .inner {
            padding: 20px;
            z-index: 2;
            position: relative;
        }

        .small-box h3 {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 0 0 10px 0;
        }

        .small-box p {
            font-size: 1rem;
            margin-bottom: 0;
            opacity: 0.9;
        }

        .small-box .icon {
            position: absolute;
            top: -10px;
            right: 15px;
            z-index: 0;
            font-size: 80px;
            color: rgba(0, 0, 0, 0.15);
        }

        .small-box-footer {
            background: rgba(0, 0, 0, 0.1);
            display: block;
            padding: 8px;
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
            text-decoration: none;
            font-weight: 600;
        }

        .small-box-footer:hover {
            color: #fff;
            background: rgba(0, 0, 0, 0.2);
        }

        /* COLORS */
        .bg-orange { background: linear-gradient(45deg, #ffc107, #ffca2c); }
        .bg-green  { background: linear-gradient(45deg, #28a745, #20c997); }
        .bg-purple { background: linear-gradient(45deg, #6f42c1, #e83e8c); }
        .bg-blue   { background: linear-gradient(45deg, #007bff, #0056b3); }
        .bg-red    { background: linear-gradient(45deg, #dc3545, #c82333); }
        .bg-cyan   { background: linear-gradient(45deg, #17a2b8, #38c1d2); }

    </style>
</head>

<body>

    <div class="d-flex" id="wrapper">
        
        <?php include 'sidebar.php'; ?>

        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-3 px-lg-4">
                <div class="d-flex align-items-center w-100">
                    <button class="btn btn-light me-3" id="sidebarToggle"><i class="fas fa-bars"></i></button>
                    <div class="ms-auto font-weight-bold text-muted small">
                        <i class="far fa-clock me-1"></i> <span id="realtimeClock">Loading...</span>
                    </div>
                </div>
            </nav>

            <div class="container-fluid px-3 px-lg-4 py-4">

                <?php if($pending_count > 0): ?>
                <div class="alert alert-warning border-start border-5 border-warning shadow-sm d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-2x me-3 text-warning d-none d-md-block"></i>
                        <div>
                            <strong>Perhatian!</strong><br class="d-md-none"> Terdapat <?php echo $pending_count; ?> transaksi baru.
                        </div>
                    </div>
                    <a href="admin_approval.php" class="btn btn-warning btn-sm text-dark fw-bold shadow-sm text-nowrap ms-2">
                        Proses <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <?php endif; ?>

                <div class="card border-0 shadow-sm mb-4 bg-white">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-center p-4">
                        <div class="mb-3 mb-md-0">
                            <h4 class="text-primary fw-bold">Selamat Datang, <?php echo $_SESSION['username']; ?>!</h4>
                            <p class="mb-0 text-muted">Ringkasan keuangan Koperasi DIKOMETA secara Real-time.</p>
                        </div>
                        <a href="transaksi_tambah.php" class="btn btn-primary shadow-sm fw-bold">
                            <i class="fas fa-plus-circle me-1"></i> Input Transaksi
                        </a>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="small-box bg-orange">
                            <div class="inner">
                                <h5>Pinjaman Kredit</h5>
                                <h3><?php echo $total_loan_count; ?> <sup style="font-size: 20px">Trans.</sup></h3>
                                <p>Dana Keluar: Rp <?php echo formatRupiah($loan_out_total); ?></p>
                            </div>
                            <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                            <a href="laporan.php?kategori=pinjaman" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right ms-1"></i></a>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="small-box bg-green">
                            <div class="inner">
                                <h5>Simpanan Anggota</h5>
                                <h3>Rp <?php echo formatRupiah($total_savings); ?></h3>
                                <p>Saldo Bersih (Masuk - Keluar)</p>
                            </div>
                            <div class="icon"><i class="fas fa-wallet"></i></div>
                            <a href="laporan.php?kategori=simpanan" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right ms-1"></i></a>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="small-box bg-purple">
                            <div class="inner">
                                <h5>Kas Koperasi</h5>
                                <h3>Rp <?php echo formatRupiah($kas_balance); ?></h3>
                                <p>Total Uang Tunai di Brankas</p>
                            </div>
                            <div class="icon"><i class="fas fa-vault"></i></div>
                            <a href="laporan.php?kategori=kas" class="small-box-footer">Lihat Detail <i class="fas fa-arrow-circle-right ms-1"></i></a>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mt-1">
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="small-box bg-blue">
                            <div class="inner">
                                <h5>Data Anggota</h5>
                                <h3><?php echo $total_members; ?> <sup style="font-size: 20px">Org</sup></h3>
                                <p>Anggota Status Aktif</p>
                            </div>
                            <div class="icon"><i class="fas fa-users"></i></div>
                            <a href="anggota.php" class="small-box-footer">Kelola Anggota <i class="fas fa-arrow-circle-right ms-1"></i></a>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="small-box bg-red">
                            <div class="inner">
                                <h5>Data Peminjam</h5>
                                <h3><?php echo $total_borrowers; ?> <sup style="font-size: 20px">Org</sup></h3>
                                <p>Anggota dengan Pinjaman</p>
                            </div>
                            <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                            <a href="laporan.php?kategori=pinjaman" class="small-box-footer">Lihat Peminjam <i class="fas fa-arrow-circle-right ms-1"></i></a>
                        </div>
                    </div>

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="small-box bg-cyan">
                            <div class="inner">
                                <h5>Data Pengguna</h5>
                                <h3><?php echo $total_users; ?> <sup style="font-size: 20px">User</sup></h3>
                                <p>Admin System</p>
                            </div>
                            <div class="icon"><i class="fas fa-user-shield"></i></div>
                            <a href="users.php" class="small-box-footer">Lihat Semua User <i class="fas fa-arrow-circle-right ms-1"></i></a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function updateClock() {
            const now = new Date();
            const options = { 
                day: 'numeric', month: 'long', year: 'numeric', 
                hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false 
            };
            document.getElementById('realtimeClock').innerText = now.toLocaleString('id-ID', options); 
        }
        updateClock();
        setInterval(updateClock, 1000);

        // Sidebar Toggle Script
        window.addEventListener('DOMContentLoaded', event => {
            const sidebarToggle = document.body.querySelector('#sidebarToggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', event => {
                    event.preventDefault();
                    document.body.classList.toggle('sb-sidenav-toggled');
                });
            }
        });
    </script>

</body>
</html>