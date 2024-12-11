<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Poliklinik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.2.96/css/materialdesignicons.min.css" rel="stylesheet">
    <style>
        :root {
            --dark-primary: #0B3C8F;
            --dark-secondary: #3165BEFF;
            --dark-light: #5985D0FF;
            --gray-dark: #adb5bd;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            background: linear-gradient(180deg, var(--dark-primary) 0%, var(--dark-secondary) 100%);
            min-height: 100vh;
            box-shadow: 4px 0 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
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
            background-color: #ffffff;
            color: var(--dark-primary);
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
            background-color: var(--dark-primary);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1rem 1.5rem;
        }

        .profile-card {
            background: linear-gradient(45deg, var(--dark-primary), var(--dark-secondary));
            color: white;
            padding: 2rem;
            border-radius: 15px;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 15px;
            border-left: 4px solid var(--dark-primary);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .btn-dark-primary {
            background-color: var(--dark-primary);
            color: white;
        }

        .btn-dark-primary:hover {
            background-color: var(--dark-secondary);
            color: white;
        }

        .mdi {
            line-height: 1;
        }

        .text-dark-primary {
            color: var(--dark-primary);
        }

        /* Badge styles */
        .badge.bg-dark-primary {
            background-color: var(--dark-primary) !important;
        }

        .badge.bg-dark-secondary {
            background-color: var(--dark-secondary) !important;
        }

        /* Custom scrollbar for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: var(--dark-secondary);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: var(--gray-dark);
            border-radius: 3px;
        }

        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #ffffff;
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
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>"
                                href="dashboard.php">
                                <i class="mdi mdi-view-dashboard me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dokter.php' ? 'active' : '' ?>"
                                href="dokter.php">
                                <i class="mdi mdi-doctor me-2"></i> Dokter
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'pasien.php' ? 'active' : '' ?>"
                                href="pasien.php">
                                <i class="mdi mdi-account-multiple me-2"></i> Pasien
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'poli.php' ? 'active' : '' ?>"
                                href="poli.php">
                                <i class="mdi mdi-hospital-building me-2"></i> Poli
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'obat.php' ? 'active' : '' ?>"
                                href="obat.php">
                                <i class="mdi mdi-pill me-2"></i> Obat
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link" href="../logout.php">
                                <i class="mdi mdi-logout me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>