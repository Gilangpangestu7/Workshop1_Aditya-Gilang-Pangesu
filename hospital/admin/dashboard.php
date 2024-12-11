<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Hitung total dokter
$sql_dokter = "SELECT COUNT(*) as total FROM dokter";
$total_dokter = $conn->query($sql_dokter)->fetch_assoc()['total'];

// Hitung total pasien
$sql_pasien = "SELECT COUNT(*) as total FROM pasien";
$total_pasien = $conn->query($sql_pasien)->fetch_assoc()['total'];

// Hitung total poli
$sql_poli = "SELECT COUNT(*) as total FROM poli";
$total_poli = $conn->query($sql_poli)->fetch_assoc()['total'];

// Hitung total obat
$sql_obat = "SELECT COUNT(*) as total FROM obat";
$total_obat = $conn->query($sql_obat)->fetch_assoc()['total'];

include 'include/header.php';
?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard Admin</h1>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <h6 class="text-muted">Total Dokter</h6>
                <div class="d-flex align-items-center">
                    <i class="mdi mdi-doctor me-2" style="font-size: 2rem; color: var(--primary);"></i>
                    <h3 class="mb-0"><?= $total_dokter ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h6 class="text-muted">Total Pasien</h6>
                <div class="d-flex align-items-center">
                    <i class="mdi mdi-account-multiple me-2" style="font-size: 2rem; color: var(--primary);"></i>
                    <h3 class="mb-0"><?= $total_pasien ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h6 class="text-muted">Total Poli</h6>
                <div class="d-flex align-items-center">
                    <i class="mdi mdi-hospital-building me-2" style="font-size: 2rem; color: var(--primary);"></i>
                    <h3 class="mb-0"><?= $total_poli ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h6 class="text-muted">Total Obat</h6>
                <div class="d-flex align-items-center">
                    <i class="mdi mdi-pill me-2" style="font-size: 2rem; color: var(--primary);"></i>
                    <h3 class="mb-0"><?= $total_obat ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Dokter Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Poli</th>
                                    <th>No. HP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT d.*, p.nama_poli 
                                       FROM dokter d 
                                       JOIN poli p ON d.id_poli = p.id 
                                       ORDER BY d.id DESC LIMIT 5";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['nama']) ?></td>
                                        <td><?= htmlspecialchars($row['nama_poli']) ?></td>
                                        <td><?= htmlspecialchars($row['no_hp']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Pasien Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>No. RM</th>
                                    <th>No. HP</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM pasien ORDER BY id DESC LIMIT 5";
                                $result = $conn->query($sql);
                                while ($row = $result->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['nama']) ?></td>
                                        <td><?= htmlspecialchars($row['no_rm']) ?></td>
                                        <td><?= htmlspecialchars($row['no_hp']) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'include/footer.php'; ?>