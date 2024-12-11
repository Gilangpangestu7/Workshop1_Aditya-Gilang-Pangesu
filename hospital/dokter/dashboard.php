<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['dokter_id'])) {
    header('Location: dokter_login.php');
    exit;
}

// Ambil data dokter
$dokter_id = $_SESSION['dokter_id'];
$sql_dokter = "SELECT d.*, p.nama_poli 
               FROM dokter d 
               JOIN poli p ON d.id_poli = p.id 
               WHERE d.id = ?";
$stmt_dokter = $conn->prepare($sql_dokter);
$stmt_dokter->bind_param("i", $dokter_id);
$stmt_dokter->execute();
$dokter = $stmt_dokter->get_result()->fetch_assoc();

// Hitung jumlah pasien hari ini
$sql_pasien_hari_ini = "SELECT COUNT(*) as total 
                        FROM daftar_poli dp 
                        JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                        WHERE jp.id_dokter = ? 
                        AND jp.hari = DAYNAME(CURRENT_DATE())
                        AND jp.status = 1";
$stmt_pasien = $conn->prepare($sql_pasien_hari_ini);
$stmt_pasien->bind_param("i", $dokter_id);
$stmt_pasien->execute();
$pasien_hari_ini = $stmt_pasien->get_result()->fetch_assoc()['total'];

// Ambil jadwal hari ini
$sql_jadwal = "SELECT * FROM jadwal_periksa 
               WHERE id_dokter = ? 
               AND hari = DAYNAME(CURDATE())
               AND status = 1";
$stmt_jadwal = $conn->prepare($sql_jadwal);
$stmt_jadwal->bind_param("i", $dokter_id);
$stmt_jadwal->execute();
$jadwal = $stmt_jadwal->get_result()->fetch_assoc();

// Ambil daftar pasien yang perlu diperiksa hari ini
$sql_daftar_pasien = "SELECT dp.*, p.nama as nama_pasien, p.no_rm,
                      pr.id as sudah_periksa
                      FROM daftar_poli dp 
                      JOIN jadwal_periksa jp ON dp.id_jadwal = jp.id
                      JOIN pasien p ON dp.id_pasien = p.id
                      LEFT JOIN periksa pr ON dp.id = pr.id_daftar_poli
                      WHERE jp.id_dokter = ? 
                      AND jp.hari = DAYNAME(CURDATE())
                      AND jp.status = 1
                      ORDER BY dp.no_antrian";
$stmt_daftar = $conn->prepare($sql_daftar_pasien);
$stmt_daftar->bind_param("i", $dokter_id);
$stmt_daftar->execute();
$daftar_pasien = $stmt_daftar->get_result();

include 'include/header.php';
?>

<!-- Main Content -->
<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
    <!-- Profile Card -->
    <div class="profile-card mt-4">
        <div class="row align-items-center">
            <div class="col-auto">
                <i class="mdi mdi-doctor" style="font-size: 4rem;"></i>
            </div>
            <div class="col">
                <h4 class="mb-1">Dr. <?= htmlspecialchars($dokter['nama']) ?></h4>
                <p class="mb-0">Poli <?= htmlspecialchars($dokter['nama_poli']) ?></p>
            </div>
            <div class="col-auto">
                <?php if ($jadwal): ?>
                    <span class="badge bg-success">Jadwal Aktif Hari Ini</span>
                <?php else: ?>
                    <span class="badge bg-warning">Tidak Ada Jadwal Hari Ini</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if (!$jadwal): ?>
        <div class="alert alert-warning mt-4">
            <i class="mdi mdi-alert"></i>
            Anda tidak memiliki jadwal aktif hari ini. Silahkan atur jadwal di menu Jadwal Periksa.
        </div>
    <?php endif; ?>

    <!-- Quick Stats -->
    <div class="row mb-4 mt-4">
        <div class="col-md-3">
            <div class="stat-card">
                <h6 class="text-muted">Pasien Hari Ini</h6>
                <div class="d-flex align-items-center">
                    <i class="mdi mdi-account-multiple me-2" style="font-size: 2rem; color: var(--cyan-primary);"></i>
                    <h3 class="mb-0"><?= $pasien_hari_ini ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h6 class="text-muted">Pasien Menunggu</h6>
                <div class="d-flex align-items-center">
                    <i class="mdi mdi-account-clock me-2" style="font-size: 2rem; color: var(--cyan-primary);"></i>
                    <h3 class="mb-0">
                        <?= $daftar_pasien->num_rows ?>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h6 class="text-muted">Jadwal Praktik</h6>
                <div class="d-flex align-items-center">
                    <i class="mdi mdi-clock-outline me-2" style="font-size: 2rem; color: var(--cyan-primary);"></i>
                    <h3 class="mb-0">
                        <?php if ($jadwal): ?>
                            <?= substr($jadwal['jam_mulai'], 0, 5) ?> - <?= substr($jadwal['jam_selesai'], 0, 5) ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <h6 class="text-muted">Status</h6>
                <div class="d-flex align-items-center">
                    <i class="mdi mdi-checkbox-marked-circle-outline me-2" style="font-size: 2rem; color: var(--cyan-primary);"></i>
                    <h3 class="mb-0"><?= $jadwal ? 'Aktif' : 'Tidak Aktif' ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Daftar Pasien Hari Ini -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Daftar Pasien Hari Ini</h5>
            <a href="memeriksa_pasien.php" class="btn btn-sm btn-light">Lihat Semua</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. Antrian</th>
                            <th>Waktu Daftar</th>
                            <th>No. RM</th>
                            <th>Nama Pasien</th>
                            <th>Keluhan</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($pasien = $daftar_pasien->fetch_assoc()): ?>
                            <tr>
                                <td><?= $pasien['no_antrian'] ?></td>
                                <td>
                                    <?php
                                    $waktu = strtotime($jadwal['jam_mulai']);
                                    $waktu = $waktu + (($pasien['no_antrian'] - 1) * 15 * 60); // 15 menit per pasien
                                    echo date('H:i', $waktu);
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($pasien['no_rm']) ?></td>
                                <td><?= htmlspecialchars($pasien['nama_pasien']) ?></td>
                                <td><?= htmlspecialchars($pasien['keluhan']) ?></td>
                                <td>
                                    <?php if ($pasien['sudah_periksa']): ?>
                                        <span class="badge bg-success">Selesai</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning">Menunggu</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!$pasien['sudah_periksa']): ?>
                                        <a href="periksa.php?id=<?= $pasien['id'] ?>" class="btn btn-sm btn-cyan">
                                            <i class="mdi mdi-stethoscope"></i> Periksa
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" disabled>
                                            <i class="mdi mdi-check"></i> Selesai
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($daftar_pasien->num_rows === 0): ?>
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada pasien yang perlu diperiksa</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include 'include/footer.php'; ?>