<?php
session_start();
include '../config/koneksi.php';

// Cek login
if (!isset($_SESSION['pasien_id'])) {
    header('Location: login.php');
    exit;
}

// Ambil data pasien
$pasien_id = $_SESSION['pasien_id'];
$sql = "SELECT * FROM pasien WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $pasien_id);
$stmt->execute();
$pasien = $stmt->get_result()->fetch_assoc();

// Ambil riwayat kunjungan
$sql_riwayat = "SELECT dp.*, p.nama_poli, d.nama as nama_dokter, pr.tgl_periksa, pr.catatan 
                FROM daftar_poli dp 
                JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                JOIN dokter d ON jp.id_dokter = d.id
                JOIN poli p ON d.id_poli = p.id
                LEFT JOIN periksa pr ON dp.id = pr.id_daftar_poli
                WHERE dp.id_pasien = ? 
                ORDER BY dp.id DESC LIMIT 5";
$stmt_riwayat = $conn->prepare($sql_riwayat);
$stmt_riwayat->bind_param("i", $pasien_id);
$stmt_riwayat->execute();
$riwayat = $stmt_riwayat->get_result();

include 'include/header.php'; ?>



<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <!-- Profile Card -->
    <div class="profile-card mt-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <i class="mdi mdi-account-circle" style="font-size: 4rem;"></i>
            </div>
            <div class="col">
                <h4 class="mb-1"><?= htmlspecialchars($pasien['nama']) ?></h4>
                <p class="mb-0">No. RM: <?= htmlspecialchars($pasien['no_rm']) ?></p>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted">Total Kunjungan</h6>
                <h3 class="mb-0">
                    <?php
                    $sql = "SELECT COUNT(*) as total FROM daftar_poli WHERE id_pasien = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $pasien_id);
                    $stmt->execute();
                    echo $stmt->get_result()->fetch_assoc()['total'];
                    ?>
                </h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted">Poli Dikunjungi</h6>
                <h3 class="mb-0">
                    <?php
                    $sql = "SELECT COUNT(DISTINCT p.id) as total 
                                       FROM daftar_poli dp 
                                       JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                                       JOIN dokter d ON jp.id_dokter = d.id
                                       JOIN poli p ON d.id_poli = p.id
                                       WHERE dp.id_pasien = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $pasien_id);
                    $stmt->execute();
                    echo $stmt->get_result()->fetch_assoc()['total'];
                    ?>
                </h3>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <h6 class="text-muted">Kunjungan Bulan Ini</h6>
                <h3 class="mb-0">
                    <?php
                    $sql = "SELECT COUNT(*) as total 
                                       FROM daftar_poli dp 
                                       JOIN periksa p ON dp.id = p.id_daftar_poli
                                       WHERE dp.id_pasien = ? AND MONTH(p.tgl_periksa) = MONTH(CURRENT_DATE())";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $pasien_id);
                    $stmt->execute();
                    echo $stmt->get_result()->fetch_assoc()['total'];
                    ?>
                </h3>
            </div>
        </div>
    </div>

    <!-- Riwayat Kunjungan Terakhir -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Riwayat Kunjungan Terakhir</h5>
            <a href="riwayat.php" class="btn btn-sm btn-light">Lihat Semua</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Poli</th>
                            <th>Dokter</th>
                            <th>Keluhan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $riwayat->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['tgl_periksa'] ?? 'Menunggu' ?></td>
                                <td><?= htmlspecialchars($row['nama_poli']) ?></td>
                                <td><?= htmlspecialchars($row['nama_dokter']) ?></td>
                                <td><?= htmlspecialchars($row['keluhan']) ?></td>
                                <td>
                                    <?php if ($row['tgl_periksa']): ?>
                                        <span class="badge bg-success">Selesai</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Menunggu</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include 'include/footer.php'; ?>