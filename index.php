<?php
// ==========================================
// 1. BACKEND LOGIC (Fetch Data from DB)
// ==========================================
include 'config.php';

// Function to format Rupiah safely
function formatRupiah($number) {
    return number_format($number, 0, ',', '.');
}

// A. Count Loans (Transactions type 'loan_out')
$loan_sql = "SELECT COUNT(*) as total FROM transactions WHERE type='loan_out'";
$total_loans = $conn->query($loan_sql)->fetch_assoc()['total'];

// B. Sum Savings (Transactions type 'saving_in')
$saving_sql = "SELECT SUM(amount) as total FROM transactions WHERE type='saving_in'";
$saving_data = $conn->query($saving_sql)->fetch_assoc();
$total_savings = $saving_data['total'] ? $saving_data['total'] : 0; 

// C. Count Active Members
$member_sql = "SELECT COUNT(*) as total FROM members WHERE status='Active'";
$total_members = $conn->query($member_sql)->fetch_assoc()['total'];

// D. Count Active Users
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
        body { background-color: var(--primary-bg); font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }
        
        /* Sidebar Styling */
        #sidebar-wrapper {
            min-height: 100vh;
            margin-left: -15rem;
            transition: margin .25s ease-out;
            background-color: #343a40; /* Darker professional theme */
            color: #fff;
        }
        #sidebar-wrapper .sidebar-heading { padding: 1rem 1.25rem; font-size: 1.2rem; font-weight: bold; background: #17a2b8; color: white; }
        #sidebar-wrapper .list-group { width: 15rem; }
        #page-content-wrapper { min-width: 100vw; transition: margin .25s ease-out; }
        
        /* Sidebar Toggled State */
        body.sb-sidenav-toggled #sidebar-wrapper { margin-left: 0; }
        @media (min-width: 768px) {
            #sidebar-wrapper { margin-left: 0; }
            #page-content-wrapper { min-width: 0; width: 100%; }
            body.sb-sidenav-toggled #sidebar-wrapper { margin-left: -15rem; }
        }

        /* Links */
        .list-group-item { border: none; padding: 15px 20px; background-color: #343a40; color: #cfd8dc; }
        .list-group-item:hover { background-color: #495057; color: #fff; }
        .list-group-item.active { background-color: #17a2b8; color: white; font-weight: bold; }
        .list-group-item i { width: 25px; text-align: center; margin-right: 10px; }

        /* Card Styling (Small Box) */
        .small-box { border-radius: 0.5rem; position: relative; display: block; margin-bottom: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); color: #fff; overflow: hidden; transition: transform 0.3s; }
        .small-box:hover { transform: translateY(-5px); box-shadow: 0 8px 12px rgba(0,0,0,0.15); }
        .small-box .inner { padding: 20px; z-index: 2; position: relative; }
        .small-box h3 { font-size: 2.2rem; font-weight: 700; margin: 0 0 10px 0; }
        .small-box p { font-size: 1rem; }
        .small-box .icon { position: absolute; top: -10px; right: 10px; z-index: 0; font-size: 90px; color: rgba(0,0,0,0.15); }
        .small-box-footer { background: rgba(0,0,0,0.1); display: block; padding: 5px; text-align: center; color: rgba(255,255,255,0.8); text-decoration: none; }
        .small-box-footer:hover { color: #fff; background: rgba(0,0,0,0.2); }

        /* Colors */
        .bg-orange { background: linear-gradient(45deg, #ffc107, #ffca2c); color: #fff; }
        .bg-green { background: linear-gradient(45deg, #28a745, #20c997); }
        .bg-purple { background: linear-gradient(45deg, #6f42c1, #e83e8c); }
        .bg-blue { background: linear-gradient(45deg, #007bff, #0056b3); }
        .bg-red { background: linear-gradient(45deg, #dc3545, #c82333); }
        .bg-cyan { background: linear-gradient(45deg, #17a2b8, #38c1d2); }
    </style>
</head>
<body>

<div class="d-flex" id="wrapper">
    <div class="border-end" id="sidebar-wrapper">
        <div class="sidebar-heading"><i class="fas fa-university me-2"></i> DIKOMETA</div>
        <div class="list-group list-group-flush">
            <a href="#" class="list-group-item list-group-item-action active"><i class="fas fa-home"></i> Beranda</a>
            <a href="#" class="list-group-item list-group-item-action"><i class="fas fa-money-bill-wave"></i> Transaksi Kas</a>
            <a href="#" class="list-group-item list-group-item-action"><i class="fas fa-wallet"></i> Simpanan</a>
            <a href="#" class="list-group-item list-group-item-action"><i class="fas fa-hand-holding-usd"></i> Pinjaman</a>
            <a href="#" class="list-group-item list-group-item-action"><i class="fas fa-file-alt"></i> Laporan</a>
            <a href="#" class="list-group-item list-group-item-action"><i class="fas fa-database"></i> Master Data</a>
            <a href="#" class="list-group-item list-group-item-action"><i class="fas fa-cogs"></i> Setting</a>
            <a href="#" class="list-group-item list-group-item-action text-danger mt-4"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>

    <div id="page-content-wrapper">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4">
            <button class="btn btn-light" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <div class="ms-auto font-weight-bold text-muted small">
                <i class="far fa-calendar-alt me-1"></i> <?php echo date('d F Y, H:i'); ?>
            </div>
        </nav>

        <div class="container-fluid px-4 py-4">
            <div class="alert alert-light border shadow-sm mb-4 d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="alert-heading text-primary">Selamat Datang, Admin!</h4>
                    <p class="mb-0 text-muted">Sistem Informasi Koperasi Digital.</p>
                </div>
                <a href="transaksi_tambah.php" class="btn btn-primary shadow-sm">
                    <i class="fas fa-plus-circle me-1"></i> Input Transaksi Baru
                </a>
            </div>

            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <div class="small-box bg-orange">
                        <div class="inner">
                            <h5>Pinjaman Kredit</h5>
                            <h3><?php echo $total_loans; ?> <sup style="font-size: 20px">Trans.</sup></h3>
                            <p>0 Tagihan Tahun Ini</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill-wave"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h5>Simpanan Anggota</h5>
                            <h3>Rp <?php echo formatRupiah($total_savings); ?></h3>
                            <p>Total Saldo Bersih</p>
                        </div>
                        <div class="icon"><i class="fas fa-wallet"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="small-box bg-purple">
                        <div class="inner">
                            <h5>Kas Koperasi</h5>
                            <h3>Rp 43.000.000</h3>
                            <p>Saldo Kas Saat Ini</p>
                        </div>
                        <div class="icon"><i class="fas fa-vault"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-lg-4 col-md-6">
                    <div class="small-box bg-blue">
                        <div class="inner">
                            <h5>Data Anggota</h5>
                            <h3><?php echo $total_members; ?> <sup style="font-size: 20px">Org</sup></h3>
                            <p>Anggota Aktif</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h5>Data Peminjam</h5>
                            <h3>0 <sup style="font-size: 20px">Org</sup></h3>
                            <p>Sedang Mengangsur</p>
                        </div>
                        <div class="icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="small-box bg-cyan">
                        <div class="inner">
                            <h5>Data Pengguna</h5>
                            <h3><?php echo $total_users; ?> <sup style="font-size: 20px">User</sup></h3>
                            <p>Admin System</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-shield"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
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