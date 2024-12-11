<?php
// Menentukan halaman aktif berdasarkan file PHP
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pasien</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        :root {
            --teal-primary: #009688;
            --teal-secondary: #26A69A;
            --teal-light: #B2DFDB;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5;
        }

        .sidebar {
            background: linear-gradient(180deg, var(--teal-primary) 0%, var(--teal-secondary) 100%);
            min-height: 100vh;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.9);
            padding: 1rem 1.5rem;
            border-radius: 8px;
            margin: 0.2rem 1rem;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar .nav-link.active {
            background-color: white;
            color: var(--teal-primary);
        }

        .main-content {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            margin: 20px;
            padding: 25px;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .card-header {
            background-color: var(--teal-primary);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .profile-card {
            background: linear-gradient(45deg, var(--teal-primary), var(--teal-secondary));
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            border-left: 4px solid var(--teal-primary);
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .btn-teal {
            background-color: var(--teal-primary);
            color: white;
        }

        .btn-teal:hover {
            background-color: var(--teal-secondary);
            color: white;
        }

        .modal-body {
            max-height: 800px;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h5 class="text-white">Sistem Poliklinik</h5>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                                <i class="mdi mdi-view-dashboard me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'poli.php') ? 'active' : ''; ?>" href="poli.php">
                                <i class="mdi mdi-hospital-building me-2"></i>
                                Daftar Poli
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'riwayat.php') ? 'active' : ''; ?>" href="riwayat.php">
                                <i class="mdi mdi-history me-2"></i>
                                Riwayat
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'profil.php') ? 'active' : ''; ?>" href="profil.php">
                                <i class="mdi mdi-account-circle me-2"></i>
                                Profil
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link text-white" href="../logout.php">
                                <i class="mdi mdi-logout me-2"></i>
                                Keluar
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>