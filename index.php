<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIKOMETA - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* General Styling */
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* Sidebar Styling */
        .sidebar { min-height: 100vh; background-color: #f8f9fa; border-right: 1px solid #dee2e6; }
        .brand-logo { background-color: #17a2b8; color: white; padding: 15px; font-weight: bold; font-size: 1.2rem; display: flex; align-items: center; }
        .sidebar a { color: #333; text-decoration: none; padding: 12px 20px; display: block; font-size: 0.95rem; border-bottom: 1px solid #eee; }
        .sidebar a:hover { background-color: #e9ecef; color: #007bff; }
        .sidebar i { width: 25px; text-align: center; margin-right: 10px; color: #555; }
        
        /* Dashboard Cards (Small Box) */
        .small-box { position: relative; display: block; margin-bottom: 20px; box-shadow: 0 1px 1px rgba(0,0,0,0.1); border-radius: 0.25rem; color: #fff; overflow: hidden; }
        .small-box .inner { padding: 20px; }
        .small-box h3 { font-size: 2.2rem; font-weight: 700; margin: 0 0 10px 0; white-space: nowrap; padding: 0; }
        .small-box p { font-size: 1rem; margin-bottom: 5px; }
        .small-box .icon { position: absolute; top: 10px; right: 10px; z-index: 0; font-size: 80px; color: rgba(0,0,0,0.15); }
        .small-box .small-box-footer { position: relative; text-align: center; padding: 3px 0; color: #fff; color: rgba(255,255,255,0.8); display: block; z-index: 10; background: rgba(0,0,0,0.1); text-decoration: none; }
        .small-box .small-box-footer:hover { color: #fff; background: rgba(0,0,0,0.15); }
        
        /* Color Utilities */
        .bg-orange { background-color: #ffc107 !important; color: #fff !important; }
        .bg-green { background-color: #28a745 !important; }
        .bg-purple { background-color: #6f42c1 !important; }
        .bg-blue { background-color: #007bff !important; }
        .bg-red { background-color: #dc3545 !important; }
        .bg-cyan { background-color: #17a2b8 !important; }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 p-0 sidebar d-none d-md-block">
            <div class="brand-logo">
                <i class="fas fa-university fa-lg me-2"></i> DIKOMETA
            </div>
            <div class="list-group list-group-flush mt-2">
                <a href="#"><i class="fas fa-home"></i> Beranda</a>
                <a href="#"><i class="fas fa-money-bill-wave"></i> Transaksi Kas</a>
                <a href="#"><i class="fas fa-wallet"></i> Simpanan</a>
                <a href="#"><i class="fas fa-hand-holding-usd"></i> Pinjaman</a>
                <a href="#"><i class="fas fa-file-alt"></i> Laporan</a>
                <a href="#"><i class="fas fa-database"></i> Master Data</a>
                <a href="#"><i class="fas fa-cogs"></i> Setting</a>
            </div>
        </div>

        <div class="col-md-10 bg-light p-4">
            <nav class="navbar navbar-light bg-white mb-4 shadow-sm rounded">
                <div class="container-fluid">
                    <span class="navbar-brand mb-0 h1"><i class="fas fa-bars me-2"></i> Beranda <small class="text-muted fs-6">Menu Utama</small></span>
                    <span class="text-muted small"><i class="far fa-calendar-alt me-1"></i> <?php echo date('d F Y H:i:s'); ?></span>
                </div>
            </nav>

            <div class="alert alert-light border-0 shadow-sm mb-4">
                <h5 class="alert-heading">Selamat Datang</h5>
                <p class="mb-0 text-muted">Hai, admin Silahkan pilih menu disamping untuk mengoperasikan aplikasi</p>
            </div>

            <div class="row">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-orange">
                        <div class="inner">
                            <h5>Pinjaman Kredit</h5>
                            <p class="mb-0"><strong>0</strong> Transaksi Bulan Ini</p>
                            <p class="mb-0"><strong>0</strong> Jumlah Tagihan Tahun Ini</p>
                            <p class="mb-0"><strong>0</strong> Sisa Tagihan Tahun Ini</p>
                        </div>
                        <div class="icon"><i class="fas fa-money-bill"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-green">
                        <div class="inner">
                            <h5>Simpanan Desember 2025</h5>
                            <p class="mb-0"><strong>3,000,000</strong> Simpanan Anggota</p>
                            <p class="mb-0"><strong>0</strong> Penarikan Tunai</p>
                            <p class="mb-0"><strong>3,000,000</strong> Jumlah Simpanan</p>
                        </div>
                        <div class="icon"><i class="fas fa-briefcase"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-purple">
                        <div class="inner">
                            <h5>Kas Bulan Desember 2025</h5>
                            <p class="mb-0"><strong>43,000,000</strong> Debet</p>
                            <p class="mb-0"><strong>0</strong> Kredit</p>
                            <p class="mb-0"><strong>43,000,000</strong> Jumlah</p>
                        </div>
                        <div class="icon"><i class="fas fa-book"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-lg-4 col-6">
                    <div class="small-box bg-blue">
                        <div class="inner">
                            <h5>Data Anggota</h5>
                            <p class="mb-0"><strong>495</strong> Anggota Aktif</p>
                            <p class="mb-0"><strong>0</strong> Anggota Tidak Aktif</p>
                            <p class="mb-0"><strong>495</strong> Jumlah Anggota</p>
                        </div>
                        <div class="icon"><i class="fas fa-user-plus"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-red">
                        <div class="inner">
                            <h5>Data Peminjam</h5>
                            <p class="mb-0"><strong>0</strong> Peminjam</p>
                            <p class="mb-0"><strong>0</strong> Sudah Lunas</p>
                            <p class="mb-0"><strong>0</strong> Belum Lunas</p>
                        </div>
                        <div class="icon"><i class="fas fa-calendar"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                <div class="col-lg-4 col-6">
                    <div class="small-box bg-cyan">
                        <div class="inner">
                            <h5>Data Pengguna</h5>
                            <p class="mb-0"><strong>4</strong> User Aktif</p>
                            <p class="mb-0"><strong>0</strong> User Non-Aktif</p>
                            <p class="mb-0"><strong>4</strong> Jumlah User</p>
                        </div>
                        <div class="icon"><i class="fas fa-users"></i></div>
                        <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div> </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>