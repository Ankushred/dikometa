<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DIKOMETA System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary-bg: #f4f6f9; }
        body { background-color: var(--primary-bg); font-family: 'Segoe UI', sans-serif; overflow-x: hidden; }
        
        /* Sidebar CSS */
        #sidebar-wrapper { min-height: 100vh; margin-left: -15rem; transition: margin .25s ease-out; background-color: #343a40; color: #fff; }
        #sidebar-wrapper .sidebar-heading { padding: 1rem 1.25rem; font-size: 1.2rem; font-weight: bold; background: #17a2b8; color: white; }
        #sidebar-wrapper .list-group { width: 15rem; }
        #page-content-wrapper { min-width: 100vw; transition: margin .25s ease-out; }
        body.sb-sidenav-toggled #sidebar-wrapper { margin-left: 0; }
        
        @media (min-width: 768px) {
            #sidebar-wrapper { margin-left: 0; }
            #page-content-wrapper { min-width: 0; width: 100%; }
            body.sb-sidenav-toggled #sidebar-wrapper { margin-left: -15rem; }
        }

        .list-group-item { border: none; padding: 15px 20px; background-color: #343a40; color: #cfd8dc; }
        .list-group-item:hover { background-color: #495057; color: #fff; }
        .list-group-item.active { background-color: #17a2b8; color: white; font-weight: bold; }
        .list-group-item i { width: 25px; text-align: center; margin-right: 10px; }
    </style>
</head>
<body>

<div class="d-flex" id="wrapper">
    
    <?php include 'sidebar.php'; ?>

    <div id="page-content-wrapper">
        
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm px-4">
            <button class="btn btn-light" id="sidebarToggle"><i class="fas fa-bars"></i></button>
            <div class="ms-auto font-weight-bold text-muted small">
                <i class="far fa-clock me-1"></i> <span id="realtimeClock">Loading...</span>
            </div>
        </nav>

        <div class="container-fluid px-4 py-4"></div>